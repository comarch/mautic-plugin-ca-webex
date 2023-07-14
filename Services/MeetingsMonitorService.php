<?php

namespace MauticPlugin\CaWebexBundle\Services;

use Mautic\LeadBundle\Entity\LeadRepository;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingParticipantsQuery;
use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingsQuery;

class MeetingsMonitorService
{
    private GetMeetingParticipantsQuery $getMeetingParticipantsQuery;
    private GetMeetingsQuery $getMeetingsQuery;

    private LeadRepository $leadRepository;

    private LeadModel $leadModel;

    /**
     * @param GetMeetingParticipantsQuery $getMeetingParticipantsQuery
     * @param GetMeetingsQuery $getMeetingsQuery
     * @param LeadRepository $leadRepository
     * @param LeadModel $leadModel
     */
    public function __construct(GetMeetingParticipantsQuery $getMeetingParticipantsQuery, GetMeetingsQuery $getMeetingsQuery, LeadRepository $leadRepository, LeadModel $leadModel)
    {
        $this->getMeetingParticipantsQuery = $getMeetingParticipantsQuery;
        $this->getMeetingsQuery = $getMeetingsQuery;
        $this->leadRepository = $leadRepository;
        $this->leadModel = $leadModel;
    }


    public function processMeeting(string $meetingId): void
    {
        $participants = $this->getMeetingParticipantsQuery->execute($meetingId);

        foreach ($participants as $participant) {
            if (!$participant->isGuest()) {
                if ($contacts = $this->leadRepository->getContactsByEmail($participant->getEmail())) {
                    $contact = current($contacts);
                } else {
                    $contact  = $this->leadModel->getEntity();
                    $data = ['email' => $participant->getEmail()];
                    $this->leadModel->setFieldValues($contact, $data, true);
                }
                $this->leadModel->setTags($contact, ["webex-{$meetingId}-participant"]);
            }
        }
    }


}