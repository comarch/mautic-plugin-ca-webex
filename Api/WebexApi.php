<?php

namespace MauticPlugin\CaWebexBundle\Api;

use Mautic\PluginBundle\Exception\ApiErrorException;
use MauticPlugin\CaWebexBundle\DataObject\WebexResponseDto;
use MauticPlugin\CaWebexBundle\Integration\WebexIntegration;
use Psr\Http\Message\ResponseInterface;

class WebexApi
{
    private const ENCODE_PARAMETERS = 'json';
    private const RETURN_RAW        = true;

    public function __construct(private WebexIntegration $integration)
    {
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @throws ApiErrorException
     */
    public function request(string $endpoint, array $parameters = [], string $method = 'GET'): WebexResponseDto
    {
        $response = $this->integration->makeRequest($this->integration->getApiUrl().$endpoint, $parameters, $method, [
            'encode_parameters' => self::ENCODE_PARAMETERS,
            'return_raw'        => self::RETURN_RAW,
        ]);

        if (is_array($response) && isset($response['error'])) {
            $responseDto = new WebexResponseDto($response['error']['code'], ['message' => $response['error']['message']]);
        } elseif ($response instanceof ResponseInterface) {
            $responseBody = json_decode($response->getBody(), true);
            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new ApiErrorException('JSON decode error: '.json_last_error_msg());
            }
            $responseDto  = new WebexResponseDto($response->getStatusCode(), $responseBody, $response->getHeaders());
        } else {
            throw new ApiErrorException('Invalid response type');
        }

        if (!$responseDto->isSuccessful()) {
            throw new ApiErrorException($responseDto->getMessage(), $responseDto->getStatusCode());
        }

        return $responseDto;
    }
}
