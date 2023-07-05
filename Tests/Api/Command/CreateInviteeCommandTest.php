<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Tests\Api\Command;

use MauticPlugin\CaWebexBundle\Api\Command\CreateInviteeCommand;
use MauticPlugin\CaWebexBundle\Api\WebexApi;
use MauticPlugin\CaWebexBundle\DataObject\WebexResponseDto;
use MauticPlugin\CaWebexBundle\Helper\WebexApiHelper;
use PHPUnit\Framework\TestCase;

class CreateInviteeCommandTest extends TestCase
{
    public function testExecuteCallsApiRequestWithCorrectParameters(): void
    {
        $meetingId   = 'meeting123';
        $email       = 'test@example.com';
        $displayName = 'John Doe';

        $apiMock = $this->createMock(WebexApi::class);
        $apiMock->expects($this->once())
            ->method('request')
            ->with('/meetingInvitees', [
                'meetingId'   => $meetingId,
                'email'       => $email,
                'displayName' => $displayName,
            ], 'POST')
            ->willReturn(new WebexResponseDto(200, []));

        $apiHelperMock = $this->createMock(WebexApiHelper::class);
        $apiHelperMock->method('getApi')->willReturn($apiMock);

        $command = new CreateInviteeCommand($apiHelperMock);
        $command->execute($meetingId, $email, $displayName);
    }

    public function testExecuteReturnsResponseBody(): void
    {
        $meetingId    = 'meeting123';
        $email        = 'test@example.com';
        $displayName  = 'John Doe';
        $responseBody = [
            'id'          => '0c8065c5490d40a19e76647f4615172e_7287778781',
            'email'       => 'test@example.com',
            'displayName' => 'John Doe',
            'coHost'      => false,
            'meetingId'   => 'meeting123',
            'panelist'    => false,
        ];

        $responseMock = $this->createMock(WebexResponseDto::class);
        $responseMock->method('getBody')->willReturn($responseBody);

        $apiMock = $this->createMock(WebexApi::class);
        $apiMock->method('request')->willReturn($responseMock);

        $apiHelperMock = $this->createMock(WebexApiHelper::class);
        $apiHelperMock->method('getApi')->willReturn($apiMock);

        $command = new CreateInviteeCommand($apiHelperMock);
        $result  = $command->execute($meetingId, $email, $displayName);

        $this->assertEquals($responseBody, $result);
    }
}
