<?php

namespace MauticPlugin\CaWebexBundle\Api;

use Mautic\PluginBundle\Exception\ApiErrorException;
use MauticPlugin\CaWebexBundle\Integration\WebexIntegration;

class WebexApi
{

    private WebexIntegration $integration;

    public function __construct(WebexIntegration $integration)
    {
        $this->integration = $integration;
    }

    public function request(string $endpoint, array $parameters = [], string $method = 'GET')
    {
        $response = $this->integration->makeRequest($this->integration->getApiUrl() . $endpoint, $parameters, $method);

        if (isset($response['error'])) {
            throw new ApiErrorException($response['error']['message'] ?? null);
        }

        return $response;
    }


    public function getMeetings(string $from = null, string $to = null, $limit = 10, $offset = 0)
    {


        return $this->request('/meetings', [
            'from' => $from,
            'to' => $to,
            'limit' => $limit,
            'offset' => $offset,
            //'meetingType' => 'test'
        ]);
    }

}
