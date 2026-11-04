<?php

namespace App\Mail\Transport;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Symfony\Component\Mailer\Exception\TransportException;

/** Application-only credentials; tokens are never persisted or supplied by a browser. */
class GraphTokenProvider
{
    private ?string $token = null;

    private int $expiresAt = 0;

    public function __construct(#[\SensitiveParameter] private readonly array $config)
    {
        foreach (['tenant_id', 'client_id'] as $key) {
            if (! is_string($config[$key] ?? null)
                || ! preg_match('/\A[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}\z/i', $config[$key])) {
                throw new InvalidArgumentException('Graph mail requires tenant and application UUIDs.');
            }
        }

        $secret = filled($config['client_secret'] ?? null);
        $certificate = filled($config['certificate_path'] ?? null) || filled($config['private_key_path'] ?? null);
        if ($secret === $certificate
            || ($certificate && (! filled($config['certificate_path'] ?? null) || ! filled($config['private_key_path'] ?? null)))) {
            throw new InvalidArgumentException('Graph mail requires either a client secret or a certificate and private key.');
        }
    }

    public function accessToken(): string
    {
        if ($this->token !== null && $this->expiresAt > now()->timestamp) {
            return $this->token;
        }

        $endpoint = 'https://login.microsoftonline.com/'.$this->config['tenant_id'].'/oauth2/v2.0/token';
        $data = [
            'client_id' => $this->config['client_id'],
            'scope' => 'https://graph.microsoft.com/.default',
            'grant_type' => 'client_credentials',
        ];
        if (filled($this->config['client_secret'] ?? null)) {
            $data['client_secret'] = $this->config['client_secret'];
        } else {
            $data['client_assertion_type'] = 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer';
            $data['client_assertion'] = $this->assertion($endpoint);
        }

        $requestedAt = now()->timestamp;
        try {
            $response = Http::asForm()->acceptJson()->connectTimeout(5)->timeout(15)
                ->withoutRedirecting()->withOptions(['verify' => true])->post($endpoint, $data);
        } catch (ConnectionException|RequestException) {
            // Never chain HTTP exceptions: they can contain credentials or provider bodies.
            throw new TransportException('Graph mail token request failed.');
        }

        $token = $response->json('access_token');
        $lifetime = $response->json('expires_in');
        $type = $response->json('token_type');
        if ($response->status() !== 200 || ! is_string($token) || $token === ''
            || preg_match('/[\x00-\x20\x7f]/', $token)
            || ! is_string($type) || strtolower($type) !== 'bearer'
            || ! is_int($lifetime) || $lifetime <= 60 || $lifetime > 86400) {
            throw new TransportException('Graph mail token request was rejected or returned an invalid token.');
        }

        $this->token = $token;
        $this->expiresAt = $requestedAt + $lifetime - 60;

        return $this->token;
    }

    public function forget(): void
    {
        $this->token = null;
        $this->expiresAt = 0;
    }

    private function assertion(string $audience): string
    {
        // Resolve local files only; configuration must not trigger URL stream requests.
        $certificatePath = realpath($this->config['certificate_path']);
        $privateKeyPath = realpath($this->config['private_key_path']);
        if (! function_exists('openssl_sign') || $certificatePath === false || $privateKeyPath === false) {
            throw new TransportException('Graph mail certificate credentials are invalid or unavailable.');
        }
        $certificate = @file_get_contents($certificatePath);
        $pem = @file_get_contents($privateKeyPath);
        $key = $pem === false ? false : @openssl_pkey_get_private($pem, $this->config['private_key_passphrase'] ?? '');
        $details = $key === false ? false : openssl_pkey_get_details($key);
        if ($certificate === false || $key === false || $details === false
            || $details['type'] !== OPENSSL_KEYTYPE_RSA || $details['bits'] < 2048
            || ! @openssl_x509_check_private_key($certificate, $key)) {
            throw new TransportException('Graph mail certificate credentials are invalid or unavailable.');
        }

        $info = openssl_x509_parse($certificate);
        if (! $info || $info['validFrom_time_t'] > now()->timestamp || $info['validTo_time_t'] <= now()->timestamp) {
            throw new TransportException('Graph mail certificate is not currently valid.');
        }

        $encode = static fn (string $value): string => rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
        $header = $encode(json_encode([
            'alg' => 'RS256', 'typ' => 'JWT',
            'x5t' => $encode(openssl_x509_fingerprint($certificate, 'sha1', true)),
        ], JSON_THROW_ON_ERROR));
        $claims = $encode(json_encode([
            'aud' => $audience,
            'iss' => $this->config['client_id'],
            'sub' => $this->config['client_id'],
            'jti' => bin2hex(random_bytes(16)),
            'nbf' => now()->timestamp - 30,
            'exp' => now()->timestamp + 300,
        ], JSON_THROW_ON_ERROR));
        if (! openssl_sign($header.'.'.$claims, $signature, $key, OPENSSL_ALGO_SHA256)) {
            throw new TransportException('Graph mail could not sign its client assertion.');
        }

        return $header.'.'.$claims.'.'.$encode($signature);
    }

    public function __debugInfo(): array
    {
        return ['credentials' => '[redacted]'];
    }
}
