<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Api\Query;

use JMS\Serializer\SerializerInterface;
use MauticPlugin\CaWebexBundle\DataObject\WebexParticipantDto;
use MauticPlugin\CaWebexBundle\Helper\WebexApiHelper;

class GetMeetingParticipantsQuery
{
    public const BATCH_LIMIT = 100;
    public const MAX_LIMIT   = 10000;

    protected WebexApiHelper $apiHelper;
    protected SerializerInterface $serializer;

    public function __construct(WebexApiHelper $apiHelper, SerializerInterface $serializer)
    {
        $this->apiHelper = $apiHelper;
        $this->serializer = $serializer;
    }

    /**
     * @throws \MauticPlugin\CaWebexBundle\Exception\ConfigurationException
     * @throws \Mautic\PluginBundle\Exception\ApiErrorException
     * @throws \Exception
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
                $participants[] = new WebexParticipantDto($item);
            }
            $nextPage     = $response->hasNextPage();
            $offset += self::BATCH_LIMIT;
        }

        return $participants;
    }
}
