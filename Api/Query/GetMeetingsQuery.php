<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Api\Query;

use MauticPlugin\CaWebexBundle\DataObject\MeetingDto;
use MauticPlugin\CaWebexBundle\Helper\WebexIntegrationHelper;

class GetMeetingsQuery
{
    public const BATCH_LIMIT = 100;
    public const MAX_LIMIT   = 500;

    protected WebexIntegrationHelper $webexIntegrationHelper;

    public function __construct(WebexIntegrationHelper $webexIntegrationHelper)
    {
        $this->webexIntegrationHelper = $webexIntegrationHelper;
    }

    /**
     * @param array<int, string> $hostEmails
     * @return array<int, MeetingDto>
     *
     * @throws \MauticPlugin\CaWebexBundle\Exception\ConfigurationException
     * @throws \Mautic\PluginBundle\Exception\ApiErrorException
     */
    public function execute(
        string $from = null,
        string $to = null,
        string $meetingType = null,
        string $scheduledType = null,
        string $state = null,
        array $hostEmails = []
    ): array {
        // pull meetings list from main account
        $meetings = $this->getMeetings($from, $to, $meetingType, $scheduledType, $state);

        // pull meetings list from other accounts from the organization
        foreach ($hostEmails as $host) {
            $meetings = array_merge(
                $meetings,
                $this->getMeetings($from, $to, $meetingType, $scheduledType, $state, $host)
            );
        }

        return $meetings;
    }

    /**
     * @return array<int, MeetingDto>
     *
     * @throws \MauticPlugin\CaWebexBundle\Exception\ConfigurationException
     * @throws \Mautic\PluginBundle\Exception\ApiErrorException
     */
    private function getMeetings(
        string $from = null,
        string $to = null,
        string $meetingType = null,
        string $scheduledType = null,
        string $state = null,
        string $hostEmail = null
    ): array {
        $meetings = [];
        $offset   = 0;
        $nextPage = true;

        while ($nextPage && $offset < self::MAX_LIMIT) {
            $payload = [
                'from'        => $from,
                'to'          => $to,
                'max'         => self::BATCH_LIMIT,
                'offset'      => $offset,
            ];

            if ($meetingType !== null) {
                $payload['meetingType'] = $meetingType;
            }
            if ($scheduledType !== null) {
                $payload['scheduledType'] = $scheduledType;
            }
            if ($state !== null) {
                $payload['state'] = $state;
            }
            if ($hostEmail !== null) {
                $payload['hostEmail'] = $hostEmail;
            }

            $response = $this->webexIntegrationHelper->getApi()->request('/meetings', $payload);

            $responseBody = $response->getBody();
            foreach ($responseBody['items'] as $item) {
                $meetings[] = new MeetingDto($item);
            }
            $nextPage     = $response->hasNextPage();
            $offset += self::BATCH_LIMIT;
        }

        return $meetings;
    }
}
