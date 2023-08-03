<?php

namespace MauticPlugin\CaWebexBundle\Services;

use Mautic\LeadBundle\Entity\LeadRepository;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingParticipantsQuery;
use MauticPlugin\CaWebexBundle\DataObject\MeetingDto;
use MauticPlugin\MauticTagManagerBundle\Entity\TagRepository;

class MeetingsMonitorService
{
    public function __construct(
        private GetMeetingParticipantsQuery $getMeetingParticipantsQuery,
        private LeadRepository $leadRepository,
        private LeadModel $leadModel,
        private TagRepository $tagRepository
    ) {
    }

    public function processMeeting(MeetingDto $meetingDto, bool $createContacts): void
    {
        $meetingId       = $meetingDto->getId();
        $attendedTagName = "webex-{$meetingId}-attended";

        // do not process meetings that hasn't ended yet or if the attended tag exists, so it's already processed
        if (!$meetingDto->hasEnded() || !empty($this->tagRepository->getTagsByName([$attendedTagName]))) {
            return;
        }

        $participants = $this->getMeetingParticipantsQuery->execute($meetingId);

        foreach ($participants as $participant) {
            if (!$participant->isGuest()) {
                if ($contacts = $this->leadRepository->getContactsByEmail($participant->getEmail())) {
                    $contact = current($contacts);
                } elseif ($createContacts) {
                    $contact  = $this->leadModel->getEntity();
                    $data     = ['email' => $participant->getEmail()];
                    $this->leadModel->setFieldValues($contact, $data, true);
                }

                if (isset($contact)) {
                    $this->leadModel->modifyTags($contact, [$attendedTagName]);
                }
            }
        }
    }
}
