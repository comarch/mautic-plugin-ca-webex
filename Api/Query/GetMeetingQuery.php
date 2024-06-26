<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Api\Query;

use MauticPlugin\CaWebexBundle\DataObject\MeetingDto;
use MauticPlugin\CaWebexBundle\Helper\WebexIntegrationHelper;

class GetMeetingQuery
{
    public function __construct(protected WebexIntegrationHelper $webexIntegrationHelper)
    {
    }

    /**
     * @throws \MauticPlugin\CaWebexBundle\Exception\ConfigurationException
     * @throws \Mautic\PluginBundle\Exception\ApiErrorException
     */
    public function execute(string $meetingId = null): MeetingDto
    {
        $response     = $this->webexIntegrationHelper->getApi()->request("/meetings/{$meetingId}");
        $responseBody = $response->getBody();

        return new MeetingDto($responseBody);
    }
}
