<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Tests\Api\Query;

use MauticPlugin\CaWebexBundle\Api\Query\GetMeetingsQuery;
use MauticPlugin\CaWebexBundle\Api\WebexApi;
use MauticPlugin\CaWebexBundle\DataObject\WebexResponseDto;
use MauticPlugin\CaWebexBundle\Helper\WebexApiHelper;
use PHPUnit\Framework\TestCase;

class GetMeetingsQueryTest extends TestCase
{
    public function testExecuteReturnsMeetings(): void
    {
        $responseBody = [
            'items' => [
                [
                    'id'            => 'da0fd046af334f249787c53604d73a96',
                    'meetingNumber' => '27415391113',
                    'title'         => 'test3',
                ],
            ],
        ];

        $apiMock = $this->createMock(WebexApi::class);
        $apiMock->method('request')->willReturn(new WebexResponseDto(200, $responseBody));

        $apiHelperMock = $this->createMock(WebexApiHelper::class);
        $apiHelperMock->method('getApi')->willReturn($apiMock);

        $query  = new GetMeetingsQuery($apiHelperMock);
        $result = $query->execute();
        $this->assertSame($responseBody['items'], $result);
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

        $apiHelperMock = $this->createMock(WebexApiHelper::class);
        $apiHelperMock->method('getApi')->willReturn($apiMock);

        $query = new GetMeetingsQuery($apiHelperMock);
        $query->execute($from, $to);
    }

    public function testExecuteReturnsMeetingsWithPagination(): void
    {
        $responseBody1 = [
            'items' => [
                [
                    'id'            => 'da0fd046af334f249787c53604d73a96',
                    'meetingNumber' => '27415391113',
                    'title'         => 'meeting1',
                ],
            ],
        ];
        $responseBody2 = [
            'items' => [
                [
                    'id'            => 'ae0bf59917af87fa5b4dc8c75668e3ca',
                    'meetingNumber' => '27415391114',
                    'title'         => 'meeting2',
                ],
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

        $query  = new GetMeetingsQuery($apiHelperMock);
        $result = $query->execute();

        $this->assertCount(2, $result);
        $this->assertEquals($responseBody1['items'][0], $result[0]);
        $this->assertEquals($responseBody2['items'][0], $result[1]);
    }
}
