<?php

declare(strict_types=1);

namespace App\Service\Notification;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PushoverNotifier
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        private readonly string $appToken,
        private readonly string $userKey,
    ) {
    }

    public function send(string $title, string $message, ?string $url = null, ?string $urlTitle = null): void
    {
        $this->logger->info('Pushover send() called', [
            'title' => $title,
            'message' => $message,
        ]);

        if ('' === trim($this->appToken) || '' === trim($this->userKey)) {
            $this->logger->warning('Pushover skipped: missing credentials.', [
                'hasAppToken' => '' !== trim($this->appToken),
                'hasUserKey' => '' !== trim($this->userKey),
            ]);

            return;
        }

        $payload = [
            'token' => $this->appToken,
            'user' => $this->userKey,
            'title' => $title,
            'message' => $message,
            'priority' => 0,
        ];

        if (null !== $url) {
            $payload['url'] = $url;
        }

        if (null !== $urlTitle) {
            $payload['url_title'] = $urlTitle;
        }

        try {
            $response = $this->httpClient->request('POST', 'https://api.pushover.net/1/messages.json', [
                'body' => $payload,
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getContent(false);

            $this->logger->info('Pushover response received', [
                'statusCode' => $statusCode,
                'content' => $content,
            ]);
        } catch (TransportExceptionInterface $exception) {
            $this->logger->error('Pushover transport request failed', [
                'message' => $exception->getMessage(),
            ]);
        } catch (\Throwable $exception) {
            $this->logger->error('Pushover request failed', [
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
