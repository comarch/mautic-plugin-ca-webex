<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Command;

use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingQuery;
use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingsQuery;
use MauticPlugin\CaWebexBundle\DataObject\MeetingStates;
use MauticPlugin\CaWebexBundle\DataObject\MeetingTypes;
use MauticPlugin\CaWebexBundle\Services\MeetingsMonitorService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class MonitorWebexMeetingsCommand extends Command
{


    public function __construct(
        private MeetingsMonitorService $meetingsMonitorService,
        private GetMeetingsQuery $getMeetingsQuery,
        private GetMeetingQuery $getMeetingQuery
    ){
        parent::__construct();
    }


    protected function configure()
    {
        $this->setName('mautic:webex:monitoring')
            ->addOption(
                'id',
                'i',
                InputOption::VALUE_OPTIONAL,
                'The id of a specific meeting to process',
            )
            ->addOption(
                'meeting-type',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set meeting type',
                MeetingTypes::MEETING_SERIES
            )
            ->addOption(
                'meeting-state',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set meeting state',
                MeetingStates::EXPIRED
            )
            ->addOption(
                'from',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set start meeting date/time in UTC timezone.',
                gmdate('Y-m-d', strtotime('-7 days'))
            )
            ->addOption(
                'to',
                null,
                InputOption::VALUE_OPTIONAL,
                'Set end meeting date/time in UTC timezone.',
                gmdate('Y-m-d H:i:s')
            )
            ->addOption(
                'create-contacts',
                null,
                InputOption::VALUE_OPTIONAL,
                'Create a new contact if a meeting participant does not exist.',
                false
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $meetingId = $input->getOption('id');
        $meetingType = $input->getOption('meeting-type');
        $meetingState = $input->getOption('meeting-state');
        $from = $input->getOption('from');
        $to = $input->getOption('to');
        $createContacts = (bool) $input->getOption('create-contacts');

        if ($meetingId) {
            $meeting = $this->getMeetingQuery->execute($meetingId);
            $this->meetingsMonitorService->processMeeting($meeting, $createContacts);
        } else {
            $meetingsCollection = $this->getMeetingsQuery->execute($from, $to, $meetingType, $meetingState);

            foreach ($meetingsCollection as $meetingDto) {
                $this->meetingsMonitorService->processMeeting($meetingDto, $createContacts);
            }
        }

        return Command::SUCCESS;
    }
}