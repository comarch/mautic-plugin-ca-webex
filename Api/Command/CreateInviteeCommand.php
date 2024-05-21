<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Api\Command;

use Mautic\PluginBundle\Exception\ApiErrorException;
use MauticPlugin\CaWebexBundle\Exception\UserIsAlreadyInvitedException;
use MauticPlugin\CaWebexBundle\Helper\WebexIntegrationHelper;

class CreateInviteeCommand
{
    public function __construct(protected WebexIntegrationHelper $webexIntegrationHelper)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ApiErrorException
     * @throws UserIsAlreadyInvitedException
     */
    public function execute(string $meetingId, string $email, string $displayName = null): array
    {
        $payload = [
            'meetingId' => $meetingId,
            'email'     => $email,
        ];

        $payload['displayName'] = $displayName ?? '';

        try {
            $response = $this->webexIntegrationHelper->getApi()->request('/meetingInvitees', $payload, 'POST');
        } catch (ApiErrorException $e) {
            if (409 === $e->getCode() && str_contains($e->getMessage(), 'User is already a meeting invitee')) {
                throw new UserIsAlreadyInvitedException($e->getMessage(), $e->getCode(), $e);
            } else {
                throw $e;
            }
        }

        return $response->getBody();
    }
}
