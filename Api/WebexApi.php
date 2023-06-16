<?php

namespace MauticPlugin\CaWebexBundle\Api;

use DateTime;
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


    public function getMeetings(string $from = null, string $to = null, $limit = 100, $offset = 0): array
    {
        return $this->request('/meetings', [
            'from' => $from,
            'to' => $to,
            'max' => $limit,
            'offset' => $offset,
        ]);
    }

    public function getFutureMeetings(): array
    {
        $date = new DateTime();
        $from = $date->format('Y-m-d');
        $date->modify('+1 year');
        $to = $date->format('Y-m-d');

        return $this->getMeetings($from, $to);
    }

    public function getMeeting(string $meetingId): array
    {
        return $this->request("/meetings/{$meetingId}");
    }

    public function getMeetingInvitees(string $meetingId, int $limit = 10, $offset = 0): array
    {
        return $this->request('/meetingInvitees', [
            'meetingId' => $meetingId,
            'max' => $limit,
            'offset' => $offset,
        ]);
    }

    public function createMeetingInvitee(string $meetingId, string $email, string $displayName = null): array
    {
        $payload = [
            'meetingId' => $meetingId,
            'email' => $email
        ];

        if (!empty($displayName)) {
            $payload['displayName'] = $displayName;
        }

        return $this->request('/meetingInvitees', $payload, 'POST');
    }

}
