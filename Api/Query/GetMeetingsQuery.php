<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Api\Query;

use MauticPlugin\CaWebexBundle\Helper\WebexApiHelper;

class GetMeetingsQuery
{
    public const BATCH_LIMIT = 100;
    public const MAX_LIMIT   = 500;

    protected WebexApiHelper $apiHelper;

    public function __construct(WebexApiHelper $webexApiHelper)
    {
        $this->apiHelper = $webexApiHelper;
    }

    /**
     * @return array<int, array<string, mixed>>
     *
     * @throws \MauticPlugin\CaWebexBundle\Exception\ConfigurationException
     * @throws \Mautic\PluginBundle\Exception\ApiErrorException
     */
    public function execute(string $from = null, string $to = null): array
    {
        $meetings = [];
        $offset   = 0;
        $nextPage = true;

        while ($nextPage && $offset < self::MAX_LIMIT) {
            $response = $this->apiHelper->getApi()->request('/meetings', [
                'from'   => $from,
                'to'     => $to,
                'max'    => self::BATCH_LIMIT,
                'offset' => $offset,
            ]);

            $responseBody = $response->getBody();
            $meetings     = array_merge($meetings, $responseBody['items']);
            $nextPage     = $response->hasNextPage();
            $offset += self::BATCH_LIMIT;
        }

        return $meetings;
    }
}
