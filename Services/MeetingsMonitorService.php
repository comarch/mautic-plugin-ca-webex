<?php

namespace MauticPlugin\CaWebexBundle\Services;

use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingParticipantsQuery;
use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingsQuery;

class MeetingsMonitorService
{
    private GetMeetingParticipantsQuery $getMeetingParticipantsQuery;
    private GetMeetingsQuery $getMeetingsQuery;

    public function __construct(GetMeetingParticipantsQuery $getMeetingParticipantsQuery, GetMeetingsQuery $getMeetingsQuery)
    {
        $this->getMeetingParticipantsQuery = $getMeetingParticipantsQuery;
        $this->getMeetingsQuery = $getMeetingsQuery;
    }

    public function processMeeting(string $meetingId): void
    {
        $result = $this->getMeetingParticipantsQuery->execute($meetingId);
    }


}