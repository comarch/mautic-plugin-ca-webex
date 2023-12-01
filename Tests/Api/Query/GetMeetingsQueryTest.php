<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Tests\Api\Query;

use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingsQuery;
use MauticPlugin\CaWebexBundle\Api\WebexApi;
use MauticPlugin\CaWebexBundle\DataObject\WebexResponseDto;
use MauticPlugin\CaWebexBundle\Helper\WebexIntegrationHelper;
use PHPUnit\Framework\TestCase;

class GetMeetingsQueryTest extends TestCase
{
    public const TEST_MEETINGS = [
        [
            'id'            => '2975a9e1b0a84d9587569326600993f3',
            'meetingNumber' => '27876788518',
            'title'         => 'Meeting 1',
            'meetingType'   => 'meetingSeries',
            'state'         => 'expired',
            'timezone'      => 'UTC',
            'start'         => '2023-07-05T08:30:00Z',
            'end'           => '2023-07-05T09:10:00Z',
            'scheduledType' => 'meeting',
        ],
        [
            'id'            => '38769d098ca98d958756932660098896',
            'meetingNumber' => '68743457898',
            'title'         => 'Meeting 2',
            'meetingType'   => 'meetingSeries',
            'state'         => 'expired',
            'timezone'      => 'UTC',
            'start'         => '2023-07-07T09:30:00Z',
            'end'           => '2023-07-07T12:30:00Z',
            'scheduledType' => 'meeting',
        ],
    ];

    public function testExecuteReturnsMeetings(): void
    {
        $responseBody = [
            'items' => [self::TEST_MEETINGS[0]],
        ];

        $apiMock = $this->createMock(WebexApi::class);
        $apiMock->method('request')->willReturn(new WebexResponseDto(200, $responseBody));

        $webexIntegrationHelperMock = $this->createMock(WebexIntegrationHelper::class);
        $webexIntegrationHelperMock->method('getApi')->willReturn($apiMock);

        $query  = new GetMeetingsQuery($webexIntegrationHelperMock);
        $result = $query->execute();
        $this->assertSame($responseBody['items'][0]['id'], $result[0]->getId());
        $this->assertSame($responseBody['items'][0]['title'], $result[0]->getTitle());
    }

    public function testExecuteCallsApiWithCorrectParameters(): void
    {
        $from   = '2023-01-01';
        $to     = '2023-12-31';
        $offset = 0;

        $apiMock = $this->createMock(WebexApi::class);
        $apiMock->expects($this->once())
            ->method('request')
            ->with('/meetings', [
                'from'   => $from,
                'to'     => $to,
                'max'    => GetMeetingsQuery::BATCH_LIMIT,
                'offset' => $offset,
            ])
            ->willReturn(new WebexResponseDto(200, ['items' => []]));

        $apiHelperMock = $this->createMock(WebexIntegrationHelper::class);
        $apiHelperMock->method('getApi')->willReturn($apiMock);

        $query = new GetMeetingsQuery($apiHelperMock);
        $query->execute($from, $to);
    }

    public function testExecuteReturnsMeetingsWithPagination(): void
    {
        $responseBody1 = [
            'items' => [self::TEST_MEETINGS[0]],
        ];
        $responseBody2 = [
            'items' => [self::TEST_MEETINGS[1]],
        ];

        $apiMock      = $this->createMock(WebexApi::class);
        $responseMock = $this->createMock(WebexResponseDto::class);
        $responseMock->method('getBody')->willReturnOnConsecutiveCalls(
            $responseBody1,
            $responseBody2,
        );
        $responseMock->method('hasNextPage')->willReturnOnConsecutiveCalls(true, false);
        $apiMock->method('request')->willReturn($responseMock);

        $apiHelperMock = $this->createMock(WebexIntegrationHelper::class);
        $apiHelperMock->method('getApi')->willReturn($apiMock);

        $query  = new GetMeetingsQuery($apiHelperMock);
        $result = $query->execute();

        $this->assertCount(2, $result);
        $this->assertSame($responseBody1['items'][0]['id'], $result[0]->getId());
        $this->assertSame($responseBody1['items'][0]['title'], $result[0]->getTitle());

        $this->assertSame($responseBody2['items'][0]['id'], $result[1]->getId());
        $this->assertSame($responseBody2['items'][0]['title'], $result[1]->getTitle());
    }

    public function testExecuteCallsApiWithOptionalParameters(): void
    {
        $from   = '2023-01-01';
        $to     = '2023-12-31';
        $meetingType     = 'scheduledMeeting';
        $state     = 'scheduled';
        $scheduledType     = 'meeting';
        $hostEmail     = 'test@example.com';
        $offset = 0;

        $apiMock = $this->createMock(WebexApi::class);
        $apiMock->expects($this->once())
            ->method('request')
            ->with('/meetings', [
                'from'   => $from,
                'to'     => $to,
                'max'    => GetMeetingsQuery::BATCH_LIMIT,
                'offset' => $offset,
                'meetingType' => $meetingType,
                'scheduledType' => $scheduledType,
                'state' => $state,
                'hostEmail' => $hostEmail
            ])
            ->willReturn(new WebexResponseDto(200, ['items' => []]));

        $apiHelperMock = $this->createMock(WebexIntegrationHelper::class);
        $apiHelperMock->method('getApi')->willReturn($apiMock);

        $query = new GetMeetingsQuery($apiHelperMock);
        $query->execute(
            from: $from,
            to: $to,
            meetingType: $meetingType,
            scheduledType: $scheduledType,
            state: $state,
            hostEmail: $hostEmail
        );
    }
}
