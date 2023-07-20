<?php

namespace MauticPlugin\CaWebexBundle\Services;

use Mautic\LeadBundle\Entity\LeadRepository;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingParticipantsQuery;
use MauticPlugin\CaWebexBundle\DataObject\MeetingDto;

class MeetingsMonitorService
{

    public function __construct(
        private GetMeetingParticipantsQuery $getMeetingParticipantsQuery,
        private LeadRepository              $leadRepository,
        private LeadModel                   $leadModel
    )
    {
    }

    public function processMeeting(MeetingDto $meetingDto): void
    {
        $meetingId = $meetingDto->getId();
        $participants = $this->getMeetingParticipantsQuery->execute($meetingId);

        if (!$meetingDto->hasEnded()) {
            return;
        }

        foreach ($participants as $participant) {
            if (!$participant->isGuest()) {
                if ($contacts = $this->leadRepository->getContactsByEmail($participant->getEmail())) {
                    $contact = current($contacts);
                } else {
                    $contact  = $this->leadModel->getEntity();
                    $data = ['email' => $participant->getEmail()];
                    $this->leadModel->setFieldValues($contact, $data, true);
                }

                $this->leadModel->modifyTags($contact, ["webex-{$meetingId}-attended"]);
            }
        }
    }

}