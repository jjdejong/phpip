<?php

namespace App\Mail\Transport;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;

class GraphTransport extends AbstractTransport
{
    public function __construct(private readonly GraphTokenProvider $tokens, private readonly string $mailbox)
    {
        parent::__construct();
        if (! filter_var($mailbox, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Graph mail requires a fixed sending mailbox email address.');
        }
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();
        if (! $email instanceof Email || count($email->getFrom()) !== 1) {
            throw new TransportException('Graph mail requires an email with exactly one From address.');
        }
        foreach (array_filter([
            $email->getFrom()[0], $email->getSender(), $email->getReturnPath(),
            $message->getEnvelope()->getSender(),
        ]) as $sender) {
            if (strcasecmp($sender->getAddress(), $this->mailbox) !== 0) {
                throw new TransportException('Graph mail sender must match the configured sending mailbox.');
            }
        }
        foreach ($email->getHeaders()->getNames() as $name) {
            if (str_starts_with(strtolower($name), 'resent-')) {
                throw new TransportException('Graph mail does not support resent message headers.');
            }
        }

        // Graph MIME has no separate SMTP envelope. Reconcile recipients before encoding,
        // including BCC (which Symfony normally strips from serialized email).
        $headers = $email->getPreparedHeaders();
        $remaining = [];
        foreach ($message->getEnvelope()->getRecipients() as $recipient) {
            $remaining[strtolower($recipient->getAddress())] = $recipient;
        }
        foreach (['To' => $email->getTo(), 'Cc' => $email->getCc()] as $name => $addresses) {
            $headers->remove($name);
            $selected = [];
            foreach ($addresses as $address) {
                $key = strtolower($address->getAddress());
                if (isset($remaining[$key])) {
                    $selected[] = $address;
                    unset($remaining[$key]);
                }
            }
            if ($selected) {
                $headers->addMailboxListHeader($name, $selected);
            }
        }
        if ($remaining) {
            $headers->addMailboxListHeader('Bcc', array_values($remaining));
        }
        $headers->remove('Message-ID');
        $headers->addIdHeader('Message-ID', $message->getMessageId());
        // AbstractPart::toString() includes Content-Type/encoding headers and then
        // the CRLF separator. An extra CRLF here would put those headers in the body.
        $mime = $headers->toString().($email->getBody()?->toString() ?? '');
        // Stay below Graph's simple send request limit, without requesting draft/read access.
        if (strlen($mime) > 3000000) {
            throw new TransportException('Graph mail message exceeds the supported MIME size of 3 MB.');
        }

        $token = $this->tokens->accessToken();
        try {
            $response = Http::withToken($token)->connectTimeout(5)->timeout(30)
                ->withoutRedirecting()->withOptions(['verify' => true])
                ->withBody(base64_encode($mime), 'text/plain')
                ->post('https://graph.microsoft.com/v1.0/users/'.rawurlencode($this->mailbox).'/sendMail');
        } catch (ConnectionException|RequestException) {
            // A timeout may occur after acceptance; automatic retries could send duplicates.
            throw new TransportException('Graph mail request failed; delivery status is unknown.');
        }
        if ($response->status() === 401) {
            $this->tokens->forget();
        }
        if ($response->status() !== 202) {
            $retry = $response->header('Retry-After');
            $delay = $response->status() === 429 && ctype_digit($retry) ? ' Retry after '.$retry.' seconds.' : '';
            throw new TransportException('Graph mail submission failed (HTTP '.$response->status().').'.$delay);
        }
    }

    public function __toString(): string
    {
        return 'microsoft-graph';
    }
}
