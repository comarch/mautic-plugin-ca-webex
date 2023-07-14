<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Command;

use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingsQuery;
use MauticPlugin\CaWebexBundle\Services\MeetingsMonitorService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class MonitorWebexMeetingsCommand extends Command
{

    private MeetingsMonitorService $meetingsMonitorService;

    public function __construct(
        MeetingsMonitorService $meetingsMonitorService
    ){
        parent::__construct();
        $this->meetingsMonitorService = $meetingsMonitorService;
    }


    protected function configure()
    {
        $this->setName('mautic:webex:monitoring')
            ->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'The id of a specific meeting to process');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $meetingId = $input->getOption('id');

        if ($meetingId) {
            $this->meetingsMonitorService->processMeeting($meetingId);
        }

        return Command::SUCCESS;
    }
}