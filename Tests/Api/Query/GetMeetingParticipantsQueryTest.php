<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Tests\Api\Query;

use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingParticipantsQuery;
use MauticPlugin\CaWebexBundle\Api\WebexApi;
use MauticPlugin\CaWebexBundle\DataObject\ParticipantDto;
use MauticPlugin\CaWebexBundle\DataObject\WebexResponseDto;
use MauticPlugin\CaWebexBundle\Helper\WebexApiHelper;
use PHPUnit\Framework\TestCase;

class GetMeetingParticipantsQueryTest extends TestCase
{

    const TEST_MEETINGS = [
        [
            'id' => '2975a9e1b0a84d9587569326600993f3',
            'meetingNumber' => '27876788518',
            'title' => 'Meeting 1',
            'meetingType' => 'meetingSeries',
            'state' => 'expired',
            'timezone' => 'UTC',
            'start' => '2023-07-05T08:30:00Z',
            'end' => '2023-07-05T09:10:00Z',
            'scheduledType' => 'meeting',
        ],
    ];

    public function testExecuteReturnsMeetings(): void
    {
        $responseBody = [
            'items' => [
                [
                    'id' => '2975a9e16544344535544326600993f3_I_265435434543222672_2dd9053c-69ec-3588-b9a0-6f13f2425329',
                    'host' => false,
                    'coHost' => false,
                    'email' => '31265ad4-5666-4691-9b30-2f608441d733@guest.webex.localhost',
                    'displayName' => 'John',
                    'invitee' => false,
                    'muted' => false,
                    'state' => 'end',
                    'joinedTime' => '2023-07-05T08:53:45Z',
                    'leftTime' => '2023-07-05T08:56:49Z',
                    'meetingId' => '2975a9e16544344535544326600993f3_I_265435434543222672',
                    'meetingStartTime' => '2023-07-05T08:47:35Z',
                ]
            ],
        ];

        $apiMock = $this->createMock(WebexApi::class);
        $apiMock->method('request')->willReturn(new WebexResponseDto(200, $responseBody));

        $apiHelperMock = $this->createMock(WebexApiHelper::class);
        $apiHelperMock->method('getApi')->willReturn($apiMock);

        $query  = new GetMeetingParticipantsQuery($apiHelperMock);
        $result = $query->execute('2975a9e16544344535544326600993f3');
        $this->assertSame($responseBody['items'][0]['id'], $result[0]->getId());
        $this->assertSame($responseBody['items'][0]['email'], $result[0]->getEmail());
    }

    public function testExecuteReturnsParticipantsWithPagination(): void
    {
        $responseBody1 = [
            'items' => [
                [
                    'id' => '2975a9e16544344535544326600993f3_I_265435434543222672_2dd9053c-69ec-3588-b9a0-6f13f2425329',
                    'host' => false,
                    'coHost' => false,
                    'email' => '31265ad4-5666-4691-9b30-2f608441d733@guest.webex.localhost',
                    'displayName' => 'John',
                    'invitee' => false,
                    'muted' => false,
                    'state' => 'end',
                    'joinedTime' => '2023-07-05T08:53:45Z',
                    'leftTime' => '2023-07-05T08:56:49Z',
                    'meetingId' => '2975a9e16544344535544326600993f3_I_265435434543222672',
                    'meetingStartTime' => '2023-07-05T08:47:35Z',
                ]
            ],
        ];
        $responseBody2 = [
            'items' => [
                [
                    'id' => '2975a9e16544344535544326600993f3_I_265435434543222672_2ca8553c-44ec-4568-b9a0-4653acd24569',
                    'host' => false,
                    'coHost' => false,
                    'email' => 'ada@example.com',
                    'displayName' => 'Ada',
                    'invitee' => true,
                    'muted' => false,
                    'state' => 'end',
                    'joinedTime' => '2023-07-05T08:54:45Z',
                    'leftTime' => '2023-07-05T08:59:49Z',
                    'meetingId' => '2975a9e16544344535544326600993f3_I_265435434543222672',
                    'meetingStartTime' => '2023-07-05T08:47:35Z',
                ]
            ],
        ];

        $apiMock      = $this->createMock(WebexApi::class);
        $responseMock = $this->createMock(WebexResponseDto::class);
        $responseMock->method('getBody')->willReturnOnConsecutiveCalls(
            $responseBody1,
            $responseBody2,
        );
        $responseMock->method('hasNextPage')->willReturnOnConsecutiveCalls(true, false);
        $apiMock->method('request')->willReturn($responseMock);

        $apiHelperMock = $this->createMock(WebexApiHelper::class);
        $apiHelperMock->method('getApi')->willReturn($apiMock);

        $query  = new GetMeetingParticipantsQuery($apiHelperMock);
        $result = $query->execute('2975a9e16544344535544326600993f3');

        $this->assertCount(2, $result);
        $this->assertSame($responseBody1['items'][0]['id'], $result[0]->getId());
        $this->assertSame($responseBody1['items'][0]['email'], $result[0]->getEmail());

        $this->assertSame($responseBody2['items'][0]['id'], $result[1]->getId());
        $this->assertSame($responseBody2['items'][0]['email'], $result[1]->getEmail());
    }
}
