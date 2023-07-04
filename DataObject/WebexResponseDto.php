<?php

namespace MauticPlugin\CaWebexBundle\DataObject;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class WebexResponseDto
{
    private int $statusCode;

    private array $body;
    private ResponseHeaderBag $headers;

    public function __construct(int $statusCode, array $body = [], array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->headers = new ResponseHeaderBag($headers);
        $this->body = $body;
    }

    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getMessage(): ?string
    {
        return $this->body['message'] ?? null;
    }

    public function hasNextPage(): bool
    {
        $linkHeader = $this->headers->get('link');
        return !empty($linkHeader) && str_contains($linkHeader, 'rel="next"');
    }

}