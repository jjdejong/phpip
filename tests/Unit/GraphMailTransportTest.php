<?php

namespace Tests\Unit;

use App\Mail\Transport\GraphTokenProvider;
use App\Mail\Transport\GraphTransport;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class GraphMailTransportTest extends TestCase
{
    private const MAILBOX = 'phpip@example.com';

    private const TOKEN_URL = 'https://login.microsoftonline.com/11111111-1111-1111-1111-111111111111/oauth2/v2.0/token';

    private const SEND_URL = 'https://graph.microsoft.com/v1.0/users/phpip%40example.com/sendMail';

    private array $settings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settings = [
            'transport' => 'graph',
            'tenant_id' => '11111111-1111-1111-1111-111111111111',
            'client_id' => '22222222-2222-2222-2222-222222222222',
            'mailbox' => self::MAILBOX,
            'client_secret' => 'private-test-secret',
        ];
        config(['mail.default' => 'graph', 'mail.mailers.graph' => $this->settings,
            'mail.from.address' => self::MAILBOX, 'mail.from.name' => 'phpIP']);
        Http::preventStrayRequests();
    }

    private function provider(int $sendStatus = 202, array $headers = []): void
    {
        Http::fake([
            self::TOKEN_URL => Http::response([
                'access_token' => 'private-test-token', 'token_type' => 'Bearer', 'expires_in' => 3600,
            ]),
            self::SEND_URL => Http::response('', $sendStatus, $headers),
        ]);
    }

    private function transport(): GraphTransport
    {
        return Mail::mailer('graph')->getSymfonyTransport();
    }

    private function email(): Email
    {
        return (new Email)->from(self::MAILBOX)->to('client@example.net')->subject('Renewal notice')->text('Hello');
    }

    public function test_laravel_mail_uses_app_only_tokens_and_fixed_graph_send_endpoint(): void
    {
        Http::fake(function (Request $request, array $options) {
            $this->assertFalse($options['allow_redirects']);
            $this->assertTrue($options['verify']);
            if ($request->url() === self::TOKEN_URL) {
                $this->assertSame('client_credentials', $request['grant_type']);
                $this->assertSame('https://graph.microsoft.com/.default', $request['scope']);
                $this->assertSame($this->settings['client_id'], $request['client_id']);
                $this->assertSame('private-test-secret', $request['client_secret']);

                return Http::response(['access_token' => 'token', 'token_type' => 'Bearer', 'expires_in' => 3600]);
            }
            $this->assertSame(self::SEND_URL, $request->url());
            $this->assertSame('POST', $request->method());
            $this->assertTrue($request->hasHeader('Authorization', 'Bearer token'));
            $this->assertTrue($request->hasHeader('Content-Type', 'text/plain'));
            $mime = base64_decode($request->body(), true);
            $this->assertStringContainsString('Subject: Password reset', $mime);
            $this->assertStringContainsString('<p>Reset link</p>', $mime);

            return Http::response('', 202);
        });

        Mail::html('<p>Reset link</p>', fn ($mail) => $mail->to('client@example.net')->subject('Password reset'));
        Http::assertSentCount(2);
    }

    public function test_mime_preserves_cc_bcc_reply_to_alternative_body_attachments_and_custom_headers(): void
    {
        $this->provider();
        $email = $this->email()->cc('colleague@example.net')->bcc('audit@example.net')
            ->replyTo('staff@example.com')->html('<p>Hello</p>')->attach('case-data', 'case.txt');
        $email->getHeaders()->addTextHeader('X-Confirm-Reading-To', 'staff@example.com');
        $this->transport()->send($email);
        Http::assertSent(function (Request $request) {
            if ($request->url() !== self::SEND_URL) {
                return false;
            }
            $mime = base64_decode($request->body(), true);
            foreach (['To: client@example.net', 'Cc: colleague@example.net', 'Bcc: audit@example.net',
                'Reply-To: staff@example.com', 'multipart/alternative', 'filename=case.txt',
                'X-Confirm-Reading-To: staff@example.com', 'Y2FzZS1kYXRh'] as $part) {
                $this->assertStringContainsString($part, $mime);
            }

            return true;
        });
    }

    public function test_mime_header_body_separator_matches_symfony_for_text_html_and_multipart(): void
    {
        $this->provider();
        $messages = [
            'text/plain' => $this->email(),
            'text/html' => $this->email()->text(null)->html('<p>Hello</p>'),
            'multipart/mixed' => $this->email()->html('<p>Hello</p>')->attach('case-data', 'case.txt'),
        ];
        foreach ($messages as $contentType => $email) {
            $email->date(new \DateTimeImmutable('2026-11-02T12:00:00+00:00'));
            $email->getHeaders()->addIdHeader('Message-ID', 'separator-test@example.com');
            $expected = $email->toString();
            $this->transport()->send($email);
            $request = Http::recorded(fn (Request $request) => $request->url() === self::SEND_URL)->last()[0];
            $mime = base64_decode($request->body(), true);

            $parts = explode("\r\n\r\n", $mime, 2);
            $this->assertCount(2, $parts, 'MIME must have a blank line between all top-level headers and content.');
            [$headers, $body] = $parts;
            [$expectedHeaders, $expectedBody] = explode("\r\n\r\n", $expected, 2);
            // Recipient reconciliation can reorder headers; their fields and the body must match.
            $fields = static function (string $block): array {
                $lines = preg_split('/\r\n(?=[^ \t])/', $block);
                sort($lines);

                return $lines;
            };
            $this->assertSame($fields($expectedHeaders), $fields($headers));
            $this->assertSame($expectedBody, $body, 'Graph content must match Symfony native serialization.');
            $this->assertStringContainsString('Content-Type: '.$contentType, $headers);
            if ($contentType === 'multipart/mixed') {
                // Part headers belong inside multipart content; its first line must be a boundary.
                $this->assertStringStartsWith('--', $body);
            } else {
                $this->assertStringContainsString('Content-Transfer-Encoding:', $headers);
                $this->assertSame($contentType === 'text/plain' ? 'Hello' : '<p>Hello</p>', $body);
            }
        }
    }

    public function test_explicit_envelope_cannot_leak_original_recipients(): void
    {
        $this->provider();
        $email = $this->email()->cc('original-cc@example.net')->bcc('original-bcc@example.net');
        $this->transport()->send($email, new Envelope(new Address(self::MAILBOX), [new Address('override@example.net')]));
        Http::assertSent(function (Request $request) {
            if ($request->url() !== self::SEND_URL) {
                return false;
            }
            $mime = base64_decode($request->body(), true);
            $this->assertStringContainsString('Bcc: override@example.net', $mime);
            foreach (['client@example.net', 'original-cc@example.net', 'original-bcc@example.net'] as $address) {
                $this->assertStringNotContainsString($address, $mime);
            }

            return true;
        });
    }

    public function test_token_is_reused_in_memory_then_renewed_before_expiry(): void
    {
        $this->provider();
        $transport = $this->transport();
        $transport->send($this->email());
        $this->travel(3500)->seconds();
        $transport->send($this->email());
        $this->travel(50)->seconds();
        $transport->send($this->email());
        Http::assertSentCount(5); // Two token requests, three submissions.
    }

    public function test_sender_spoofing_is_rejected_before_token_acquisition(): void
    {
        foreach (['from', 'sender', 'returnPath'] as $method) {
            try {
                $this->transport()->send($this->email()->$method('other@example.com'));
                $this->fail('Sender mismatch was accepted.');
            } catch (TransportException $exception) {
                $this->assertStringContainsString('configured sending mailbox', $exception->getMessage());
            }
        }
        Http::assertNothingSent();
    }

    public function test_different_envelope_sender_and_resent_headers_are_rejected(): void
    {
        try {
            $this->transport()->send($this->email(), new Envelope(new Address('other@example.com'), [new Address('client@example.net')]));
            $this->fail('Envelope sender mismatch was accepted.');
        } catch (TransportException) {
            Http::assertNothingSent();
        }
        $email = $this->email();
        $email->getHeaders()->addTextHeader('Resent-From', 'other@example.com');
        $this->expectException(TransportException::class);
        $this->transport()->send($email);
    }

    public function test_missing_ambiguous_and_invalid_credentials_are_rejected_without_http(): void
    {
        foreach ([['client_secret' => null], ['certificate_path' => '/tmp/cert', 'private_key_path' => '/tmp/key'],
            ['tenant_id' => 'common'], ['client_id' => '../other-tenant']] as $changes) {
            try {
                new GraphTokenProvider(array_replace($this->settings, $changes));
                $this->fail('Invalid credentials were accepted.');
            } catch (InvalidArgumentException) {
                Http::assertNothingSent();
            }
        }
    }

    public function test_invalid_mailbox_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new GraphTransport(new GraphTokenProvider($this->settings), '../other-user');
    }

    public function test_expired_or_malformed_token_never_reaches_graph(): void
    {
        $changes = [['expires_in' => 10], ['access_token' => "token\r\nInjected: value"],
            ['access_token' => null], ['token_type' => 'Other'], ['token_type' => ['Bearer']]];
        $sequence = Http::sequence();
        foreach ($changes as $change) {
            $sequence->push(array_replace([
                'access_token' => 'token', 'expires_in' => 3600, 'token_type' => 'Bearer',
            ], $change));
        }
        Http::fake([self::TOKEN_URL => $sequence]);
        foreach ($changes as $change) {
            try {
                (new GraphTokenProvider($this->settings))->accessToken();
                $this->fail('Invalid token was accepted.');
            } catch (TransportException $exception) {
                $this->assertNull($exception->getPrevious());
            }
        }
        Http::assertNotSent(fn (Request $request) => str_contains($request->url(), 'graph.microsoft.com'));
    }

    public function test_token_errors_and_redirects_do_not_leak_provider_bodies_or_follow_redirects(): void
    {
        $sequence = Http::sequence();
        foreach ([400, 401, 302] as $status) {
            $sequence->push('sensitive-provider-body', $status, ['Location' => 'https://other.example.net']);
        }
        Http::fake([self::TOKEN_URL => $sequence]);
        foreach ([400, 401, 302] as $status) {
            try {
                (new GraphTokenProvider($this->settings))->accessToken();
                $this->fail('Failed token acquisition was accepted.');
            } catch (TransportException $exception) {
                $this->assertStringNotContainsString('sensitive-provider-body', $exception->getMessage());
                $this->assertNull($exception->getPrevious());
            }
        }
        Http::assertNotSent(fn (Request $request) => $request->url() !== self::TOKEN_URL);
    }

    public function test_forbidden_throttled_redirected_and_server_error_submissions_fail_without_retry_or_fallback(): void
    {
        $sequence = Http::sequence();
        foreach ([403, 429, 302, 500, 200] as $status) {
            $sequence->push('', $status, ['Retry-After' => '60']);
        }
        Http::fake([
            self::TOKEN_URL => Http::response(['access_token' => 'token', 'token_type' => 'Bearer', 'expires_in' => 3600]),
            self::SEND_URL => $sequence,
        ]);
        foreach ([403, 429, 302, 500, 200] as $status) {
            try {
                $this->transport()->send($this->email());
                $this->fail('Graph failure was accepted.');
            } catch (TransportException $exception) {
                $this->assertStringContainsString('HTTP '.$status, $exception->getMessage());
                if ($status === 429) {
                    $this->assertStringContainsString('Retry after 60 seconds', $exception->getMessage());
                }
                $this->assertNull($exception->getPrevious());
            }
        }
        Http::assertSentCount(6); // One token acquisition, five submissions.
    }

    public function test_rejected_token_is_forgotten_for_next_send_without_replaying_current_message(): void
    {
        Http::fake([
            self::TOKEN_URL => Http::response(['access_token' => 'token', 'token_type' => 'Bearer', 'expires_in' => 3600]),
            self::SEND_URL => Http::sequence()->push('', 401)->push('', 202),
        ]);
        try {
            $this->transport()->send($this->email());
            $this->fail('Rejected access token was accepted.');
        } catch (TransportException) {
            Http::assertSentCount(2);
        }
        $this->transport()->send($this->email());
        Http::assertSentCount(4);
    }

    public function test_submission_timeout_is_sanitized_and_not_retried(): void
    {
        Http::fake([
            self::TOKEN_URL => Http::response(['access_token' => 'token', 'token_type' => 'Bearer', 'expires_in' => 3600]),
            self::SEND_URL => Http::failedConnection('sensitive-transport-details'),
        ]);
        try {
            $this->transport()->send($this->email());
            $this->fail('Timeout was accepted.');
        } catch (TransportException $exception) {
            $this->assertStringContainsString('delivery status is unknown', $exception->getMessage());
            $this->assertStringNotContainsString('sensitive', $exception->getMessage());
            $this->assertNull($exception->getPrevious());
        }
        Http::assertSentCount(2);
    }

    public function test_oversized_message_is_rejected_without_token_or_submission(): void
    {
        $this->expectException(TransportException::class);
        try {
            $this->transport()->send($this->email()->text(str_repeat('x', 3000001)));
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_certificate_authentication_signs_a_short_lived_assertion_for_this_tenant(): void
    {
        $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        $csr = openssl_csr_new(['commonName' => 'phpIP test'], $key, ['digest_alg' => 'sha256']);
        $certificate = openssl_csr_sign($csr, null, $key, 1, ['digest_alg' => 'sha256']);
        openssl_x509_export($certificate, $certPem);
        openssl_pkey_export($key, $keyPem);
        $certPath = tempnam(sys_get_temp_dir(), 'graph-cert-');
        $keyPath = tempnam(sys_get_temp_dir(), 'graph-key-');
        file_put_contents($certPath, $certPem);
        file_put_contents($keyPath, $keyPem);
        try {
            $this->provider();
            $tokens = new GraphTokenProvider(array_replace($this->settings, [
                'client_secret' => null, 'certificate_path' => $certPath, 'private_key_path' => $keyPath,
            ]));
            $this->assertSame('private-test-token', $tokens->accessToken());
            Http::assertSent(function (Request $request) use ($certificate) {
                $this->assertArrayNotHasKey('client_secret', $request->data());
                $this->assertSame('urn:ietf:params:oauth:client-assertion-type:jwt-bearer', $request['client_assertion_type']);
                [$header, $payload, $signature] = explode('.', $request['client_assertion']);
                $decode = fn ($value) => base64_decode(strtr($value, '-_', '+/'));
                $claims = json_decode($decode($payload), true);
                $this->assertSame('RS256', json_decode($decode($header), true)['alg']);
                $this->assertSame(self::TOKEN_URL, $claims['aud']);
                $this->assertSame($this->settings['client_id'], $claims['iss']);
                $this->assertSame($claims['iss'], $claims['sub']);
                $this->assertLessThanOrEqual(now()->timestamp + 300, $claims['exp']);
                $this->assertSame(1, openssl_verify($header.'.'.$payload, $decode($signature), $certificate, OPENSSL_ALGO_SHA256));

                return true;
            });
        } finally {
            unlink($certPath);
            unlink($keyPath);
        }
    }

    public function test_unavailable_certificate_fails_before_any_network_request(): void
    {
        $tokens = new GraphTokenProvider(array_replace($this->settings, [
            'client_secret' => null, 'certificate_path' => '/missing/cert.pem', 'private_key_path' => '/missing/key.pem',
        ]));
        $this->expectException(TransportException::class);
        try {
            $tokens->accessToken();
        } finally {
            Http::assertNothingSent();
        }
    }
}
