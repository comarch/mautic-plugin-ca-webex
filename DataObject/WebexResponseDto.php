<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\DataObject;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class WebexResponseDto
{
    private ResponseHeaderBag $headers;

    /**
     * @param array<string, mixed> $body
     * @param array<string, mixed> $headers
     */
    public function __construct(private int $statusCode, private array $body = [], array $headers = [])
    {
        $this->headers    = new ResponseHeaderBag($headers);
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * @return array<string, mixed>
     */
    public function getBody(): array
    {
        return $this->body;
    }

    public function getMessage(): ?string
    {
        return $this->body['message'] ?? null;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function hasNextPage(): bool
    {
        $linkHeader = $this->headers->get('link');

        return !empty($linkHeader) && str_contains($linkHeader, 'rel="next"');
    }
}
