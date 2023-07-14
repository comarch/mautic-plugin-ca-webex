<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Api\Query;

use MauticPlugin\CaWebexBundle\DataObject\ParticipantDto;
use MauticPlugin\CaWebexBundle\Helper\WebexApiHelper;

class GetMeetingParticipantsQuery
{
    public const BATCH_LIMIT = 100;
    public const MAX_LIMIT   = 10000;

    protected WebexApiHelper $apiHelper;

    public function __construct(WebexApiHelper $apiHelper)
    {
        $this->apiHelper = $apiHelper;
    }

    /**
     * @param string $meetingId
     * @return array<int, ParticipantDto>
     * @throws \MauticPlugin\CaWebexBundle\Exception\ConfigurationException
     * @throws \Mautic\PluginBundle\Exception\ApiErrorException
     */
    public function execute(string $meetingId): array
    {
        $participants = [];
        $offset   = 0;
        $nextPage = true;

        while ($nextPage && $offset < self::MAX_LIMIT) {
            $response = $this->apiHelper->getApi()->request('/meetingParticipants', [
                'meetingId'   => $meetingId,
                'max'    => self::BATCH_LIMIT,
                'offset' => $offset,
            ]);

            $responseBody = $response->getBody();
            foreach($responseBody['items'] as $item) {
                $participants[] = new ParticipantDto($item);
            }
            $nextPage     = $response->hasNextPage();
            $offset += self::BATCH_LIMIT;
        }

        return $participants;
    }
}
