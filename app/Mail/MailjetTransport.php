<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\MessageConverter;

class MailjetTransport extends AbstractTransport
{
    /**
     * Create a new Mailjet Transport instance.
     */
    public function __construct(
        protected string $apiKey,
        protected string $apiSecret
    ) {
        parent::__construct();
    }

    /**
     * Send the given message via Mailjet Send API v3.1.
     */
    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $fromAddress = $email->getFrom()[0] ?? null;
        $from = [
            'Email' => $fromAddress ? $fromAddress->getAddress() : config('mail.from.address'),
            'Name' => $fromAddress ? ($fromAddress->getName() ?: config('mail.from.name')) : config('mail.from.name'),
        ];

        $to = [];
        foreach ($email->getTo() as $address) {
            $to[] = array_filter([
                'Email' => $address->getAddress(),
                'Name' => $address->getName() ?: null,
            ]);
        }

        $cc = [];
        foreach ($email->getCc() as $address) {
            $cc[] = array_filter([
                'Email' => $address->getAddress(),
                'Name' => $address->getName() ?: null,
            ]);
        }

        $bcc = [];
        foreach ($email->getBcc() as $address) {
            $bcc[] = array_filter([
                'Email' => $address->getAddress(),
                'Name' => $address->getName() ?: null,
            ]);
        }

        $messagePayload = array_filter([
            'From' => $from,
            'To' => $to,
            'Cc' => ! empty($cc) ? $cc : null,
            'Bcc' => ! empty($bcc) ? $bcc : null,
            'Subject' => $email->getSubject() ?: 'LearnBuddy Notification',
            'TextPart' => $email->getTextBody() ?: strip_tags((string) $email->getHtmlBody()),
            'HTMLPart' => $email->getHtmlBody() ?: nl2br((string) $email->getTextBody()),
        ]);

        $response = Http::withBasicAuth($this->apiKey, $this->apiSecret)
            ->acceptJson()
            ->timeout(20)
            ->post('https://api.mailjet.com/v3.1/send', [
                'Messages' => [$messagePayload],
            ]);

        if (! $response->successful()) {
            Log::error('Mailjet API delivery failure: ' . $response->body());
            throw new RuntimeException('Mailjet API error (' . $response->status() . '): ' . $response->body());
        }
    }

    /**
     * Get the string representation of the transport.
     */
    public function __toString(): string
    {
        return 'mailjet';
    }
}
