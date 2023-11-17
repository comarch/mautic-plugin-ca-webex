<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Api\Command;

use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\CaWebexBundle\Helper\WebexIntegrationHelper;

class CreateRegistrantCommand
{
    public function __construct(protected WebexIntegrationHelper $webexIntegrationHelper)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \MauticPlugin\CaWebexBundle\Exception\ConfigurationException
     * @throws \Mautic\PluginBundle\Exception\ApiErrorException
     */
    public function execute(string $meetingId, Lead $lead): array
    {
        $payload = [
            'email'         => $lead->getEmail(),
            'firstName'     => $lead->getFirstname(),
            'lastName'      => $lead->getLastname(),
        ];

        $response = $this->webexIntegrationHelper->getApi()
            ->request("/meetings/{$meetingId}/registrants", $payload, 'POST');

        return $response->getBody();
    }
}
