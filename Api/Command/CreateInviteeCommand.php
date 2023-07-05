<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Api\Command;

use MauticPlugin\CaWebexBundle\Helper\WebexApiHelper;

class CreateInviteeCommand
{
    protected WebexApiHelper $apiHelper;

    public function __construct(WebexApiHelper $webexApiHelper)
    {
        $this->apiHelper = $webexApiHelper;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \MauticPlugin\CaWebexBundle\Exception\ConfigurationException
     * @throws \Mautic\PluginBundle\Exception\ApiErrorException
     */
    public function execute(string $meetingId, string $email, string $displayName = null): array
    {
        $payload = [
            'meetingId' => $meetingId,
            'email'     => $email,
        ];

        if (!empty($displayName)) {
            $payload['displayName'] = $displayName;
        }

        $response = $this->apiHelper->getApi()->request('/meetingInvitees', $payload, 'POST');

        return $response->getBody();
    }
}
