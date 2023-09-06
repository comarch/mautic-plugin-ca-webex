<?php

namespace MauticPlugin\CaWebexBundle\Tests\Services;

use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Entity\LeadRepository;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingParticipantsQuery;
use MauticPlugin\CaWebexBundle\DataObject\MeetingDto;
use MauticPlugin\CaWebexBundle\DataObject\ParticipantDto;
use MauticPlugin\CaWebexBundle\Services\MeetingsMonitorService;
use MauticPlugin\MauticTagManagerBundle\Entity\TagRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MeetingsMonitorServiceTest extends TestCase
{
    private GetMeetingParticipantsQuery|MockObject $getMeetingParticipantsQuery;
    private LeadRepository|MockObject $leadRepository;
    private LeadModel|MockObject $leadModel;
    private TagRepository|MockObject $tagRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock dependencies
        $this->getMeetingParticipantsQuery = $this->createMock(GetMeetingParticipantsQuery::class);
        $this->leadRepository              = $this->createMock(LeadRepository::class);
        $this->leadModel                   = $this->createMock(LeadModel::class);
        $this->tagRepository               = $this->createMock(TagRepository::class);
    }

    public function testTagsAddedForAttendeesWhenMeetingEndedAndContactExists(): void
    {
        $meetingDto   = new MeetingDto($this->sampleMeetingProvider());
        $participant1 = new ParticipantDto($this->sampleParticipantProvider());

        $contact = new Lead();
        $contact->setEmail('participant1@example.com');
        $contacts = [$contact];

        $this->getMeetingParticipantsQuery->expects($this->once())
            ->method('execute')
            ->with($meetingDto->getId())
            ->willReturn([$participant1]);

        $this->leadRepository->expects($this->once())
            ->method('getContactsByEmail')
            ->with('participant1@example.com')
            ->willReturn($contacts);

        $this->leadModel->expects($this->once())
            ->method('modifyTags')
            ->with($contact, ['webex-'.$meetingDto->getId().'-attended']);

        $this->tagRepository->expects($this->once())
            ->method('getTagsByName')
            ->with(['webex-'.$meetingDto->getId().'-attended'])
            ->willReturn([]);

        // Create the service instance with mocked dependencies
        $service = new MeetingsMonitorService($this->getMeetingParticipantsQuery, $this->leadRepository, $this->leadModel, $this->tagRepository);

        // Perform the test
        $service->processMeeting($meetingDto, false);
    }

    public function testTagsAddedForAttendeesWhenMeetingEndedAndCreateContactsIsTrue(): void
    {
        $meetingDto     = new MeetingDto($this->sampleMeetingProvider());
        $participantDto = new ParticipantDto($this->sampleParticipantProvider());

        // Prepare the mock objects with appropriate return values and expectations
        $this->getMeetingParticipantsQuery->expects($this->once())
            ->method('execute')
            ->with($meetingDto->getId())
            ->willReturn([$participantDto]);

        // Simulate that no contact is found in LeadRepository
        $this->leadRepository->expects($this->once())
            ->method('getContactsByEmail')
            ->with('participant1@example.com')
            ->willReturn([]);

        // Expect the LeadModel to be called to create a new contact
        $contact = new Lead();
        $contact->setEmail('participant1@example.com');

        $this->leadModel->expects($this->once())
            ->method('getEntity')
            ->willReturn($contact);

        $this->leadModel->expects($this->once())
            ->method('setFieldValues')
            ->with($contact, ['email' => 'participant1@example.com'], true);

        $this->leadModel->expects($this->once())
            ->method('modifyTags')
            ->with($contact, ['webex-'.$meetingDto->getId().'-attended']);

        $this->tagRepository->expects($this->once())
            ->method('getTagsByName')
            ->with(['webex-'.$meetingDto->getId().'-attended'])
            ->willReturn([]);

        // Create the service instance with mocked dependencies
        $service = new MeetingsMonitorService($this->getMeetingParticipantsQuery, $this->leadRepository, $this->leadModel, $this->tagRepository);

        // Perform the test with createContacts set to true
        $service->processMeeting($meetingDto, true);
    }

    public function testTagsNotAddedForGuestsWhenMeetingEnded(): void
    {
        $meetingDto = new MeetingDto($this->sampleMeetingProvider());

        // Initialize ParticipantDto with array data for a guest participant
        $guestParticipantData = [
            'id'               => '2975a9e16544344535544326600993f3_I_265435434543222672_2ca8553c-44ec-4568-b9a0-4653acd24569',
            'host'             => false,
            'coHost'           => false,
            'email'            => '44ec-4568-b9a0-4653acd24569@guest.webex.localhost',
            'displayName'      => 'Guest1',
            'invitee'          => true,
            'muted'            => false,
            'state'            => 'end',
            'joinedTime'       => '2023-07-05T08:54:45Z',
            'leftTime'         => '2023-07-05T08:59:49Z',
            'meetingId'        => '2975a9e16544344535544326600993f3_I_265435434543222672',
            'meetingStartTime' => '2023-07-05T08:47:35Z',
        ];
        $guestParticipantDto = new ParticipantDto($guestParticipantData);

        // Prepare the mock objects with appropriate return values and expectations
        $this->getMeetingParticipantsQuery->expects($this->once())
            ->method('execute')
            ->with($meetingDto->getId())
            ->willReturn([$guestParticipantDto]);

        $this->tagRepository->expects($this->once())
            ->method('getTagsByName')
            ->with(['webex-'.$meetingDto->getId().'-attended'])
            ->willReturn([]);

        // Since the participant is a guest, we don't expect the LeadRepository or LeadModel methods to be called
        $this->leadRepository->expects($this->never())->method('getContactsByEmail');
        $this->leadModel->expects($this->never())->method('getEntity');
        $this->leadModel->expects($this->never())->method('setFieldValues');
        $this->leadModel->expects($this->never())->method('modifyTags');

        // Create the service instance with mocked dependencies
        $service = new MeetingsMonitorService($this->getMeetingParticipantsQuery, $this->leadRepository, $this->leadModel, $this->tagRepository);

        // Perform the test
        $service->processMeeting($meetingDto, false);
    }

    public function testTagsNotAddedForAttendeesWhenNoContactFoundAndCreateContactsIsFalse(): void
    {
        $meetingDto     = new MeetingDto($this->sampleMeetingProvider());
        $participantDto = new ParticipantDto($this->sampleParticipantProvider());

        // Prepare the mock objects with appropriate return values and expectations
        $this->getMeetingParticipantsQuery->expects($this->once())
            ->method('execute')
            ->with($meetingDto->getId())
            ->willReturn([$participantDto]);

        $this->tagRepository->expects($this->once())
            ->method('getTagsByName')
            ->with(['webex-'.$meetingDto->getId().'-attended'])
            ->willReturn([]);

        // Simulate that no contact is found in LeadRepository
        $this->leadRepository->expects($this->once())
            ->method('getContactsByEmail')
            ->with('participant1@example.com')
            ->willReturn([]);

        // We expect that no calls should be made to the LeadModel methods since createContacts is false
        $this->leadModel->expects($this->never())->method('getEntity');
        $this->leadModel->expects($this->never())->method('setFieldValues');
        $this->leadModel->expects($this->never())->method('modifyTags');

        // Create the service instance with mocked dependencies
        $service = new MeetingsMonitorService($this->getMeetingParticipantsQuery, $this->leadRepository, $this->leadModel, $this->tagRepository);

        // Perform the test with createContacts set to false
        $service->processMeeting($meetingDto, false);
    }

    public function testNoModificationsWhenTagAlreadyExists(): void
    {
        $meetingDto = new MeetingDto($this->sampleMeetingProvider());

        // Simulate that the tag already exists in the TagRepository
        $this->tagRepository->expects($this->once())
            ->method('getTagsByName')
            ->with(["webex-{$meetingDto->getId()}-attended"])
            ->willReturn([['tag_id' => 123, 'name' => "webex-{$meetingDto->getId()}-attended"]]);

        // We expect that no calls should be made to the GetMeetingParticipantsQuery, LeadRepository or LeadModel methods
        $this->getMeetingParticipantsQuery->expects($this->never())->method('execute');
        $this->leadRepository->expects($this->never())->method('getContactsByEmail');
        $this->leadModel->expects($this->never())->method('getEntity');
        $this->leadModel->expects($this->never())->method('setFieldValues');
        $this->leadModel->expects($this->never())->method('modifyTags');

        // Create the service instance with mocked dependencies
        $service = new MeetingsMonitorService(
            $this->getMeetingParticipantsQuery,
            $this->leadRepository,
            $this->leadModel,
            $this->tagRepository
        );

        // Perform the test
        $service->processMeeting($meetingDto, false);
    }

    /**
     * @return array<string, string>
     */
    private function sampleMeetingProvider(): array
    {
        return [
            'id'            => '2975a9e1b0a84d9587569326600993f3',
            'meetingNumber' => '27876788518',
            'title'         => 'Meeting 1',
            'meetingType'   => 'meetingSeries',
            'state'         => 'expired',
            'timezone'      => 'UTC',
            'start'         => '2023-07-05T08:30:00Z',
            'end'           => '2023-07-05T09:10:00Z',
            'scheduledType' => 'meeting',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function sampleParticipantProvider(): array
    {
        return [
            'id'               => '2975a9e16544344535544326600993f3_I_265435434543222672_2ca8553c-44ec-4568-b9a0-4653acd24569',
            'host'             => false,
            'coHost'           => false,
            'email'            => 'participant1@example.com',
            'displayName'      => 'Participant1',
            'invitee'          => true,
            'muted'            => false,
            'state'            => 'end',
            'joinedTime'       => '2023-07-05T08:54:45Z',
            'leftTime'         => '2023-07-05T08:59:49Z',
            'meetingId'        => '2975a9e16544344535544326600993f3_I_265435434543222672',
            'meetingStartTime' => '2023-07-05T08:47:35Z',
        ];
    }
}
