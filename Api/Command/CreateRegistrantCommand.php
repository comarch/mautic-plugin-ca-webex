<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Api\Command;

use Mautic\LeadBundle\Entity\Lead;
use Mautic\PluginBundle\Exception\ApiErrorException;
use MauticPlugin\CaWebexBundle\Exception\UserIsAlreadyRegisteredException;
use MauticPlugin\CaWebexBundle\Helper\WebexIntegrationHelper;

class CreateRegistrantCommand
{
    public function __construct(protected WebexIntegrationHelper $webexIntegrationHelper)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ApiErrorException
     * @throws UserIsAlreadyRegisteredException
     */
    public function execute(string $meetingId, Lead $lead): array
    {
        $payload = [
            'email'         => $lead->getEmail(),
            'firstName'     => $lead->getFirstname(),
            'lastName'      => $lead->getLastname(),
        ];

        try {
            $response = $this->webexIntegrationHelper->getApi()
                ->request("/meetings/{$meetingId}/registrants", $payload, 'POST');
        } catch (ApiErrorException $e) {
            if (409 === $e->getCode() && str_contains($e->getMessage(), 'User is already registered')) {
                throw new UserIsAlreadyRegisteredException($e->getMessage(), $e->getCode(), $e);
            } else {
                throw $e;
            }
        }

        return $response->getBody();
    }
}
