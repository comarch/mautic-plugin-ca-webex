<?php

namespace MauticPlugin\CaWebexBundle\Api;

use Mautic\PluginBundle\Exception\ApiErrorException;
use MauticPlugin\CaWebexBundle\DataObject\WebexResponseDto;
use MauticPlugin\CaWebexBundle\Integration\WebexIntegration;
use Psr\Http\Message\ResponseInterface;

class WebexApi
{

    private WebexIntegration $integration;

    public function __construct(WebexIntegration $integration)
    {
        $this->integration = $integration;
    }

    public function request(string $endpoint, array $parameters = [], string $method = 'GET'): WebexResponseDto
    {
        $response = $this->integration->makeRequest($this->integration->getApiUrl() . $endpoint, $parameters, $method, [
            'return_raw' => true
        ]);

        if (is_array($response) && isset($response['error'])) {
            $responseDto = new WebexResponseDto($response['error']['code'], ['message' => $response['error']['message']]);
        } else if ($response instanceof ResponseInterface) {
            $responseBody = json_decode($response->getBody(), true);
            $responseDto = new WebexResponseDto($response->getStatusCode(), $responseBody, $response->getHeaders());
        } else {
            throw new ApiErrorException();
        }

        if (!$responseDto->isSuccessful()) {
            throw new ApiErrorException($responseDto->getMessage());
        }

        return $responseDto;
    }

}
