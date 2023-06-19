<?php

namespace MauticPlugin\CaWebexBundle\Api\Command;

use MauticPlugin\CaWebexBundle\Api\Query\AbstractQuery;

class CreateInviteeCommand extends AbstractQuery
{
    public function execute(string $meetingId, string $email, string $displayName = null): array
    {
        $payload = [
            'meetingId' => $meetingId,
            'email' => $email
        ];

        if (!empty($displayName)) {
            $payload['displayName'] = $displayName;
        }

        $response = $this->apiHelper->getApi()->request('/meetingInvitees', $payload, 'POST');
        return $response->getBody();
    }
}