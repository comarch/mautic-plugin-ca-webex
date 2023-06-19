<?php

namespace MauticPlugin\CaWebexBundle\Api\Query;

use DateTime;
use MauticPlugin\CaWebexBundle\Helper\WebexApiHelper;

class GetFutureMeetingsQuery extends AbstractQuery
{
    private GetMeetingsQuery $getMeetingsQuery;

    public function __construct(WebexApiHelper $webexApiHelper, GetMeetingsQuery $getMeetingsQuery)
    {
        parent::__construct($webexApiHelper);
        $this->getMeetingsQuery = $getMeetingsQuery;
    }

    public function execute(): array
    {
        $date = new DateTime();
        $from = $date->format('Y-m-d');
        $date->modify('+1 year');
        $to = $date->format('Y-m-d');

        return $this->getMeetingsQuery->execute($from, $to);
    }
}