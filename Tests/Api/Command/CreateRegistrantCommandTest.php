<?php

declare(strict_types=1);

namespace MauticPlugin\CaWebexBundle\Tests\Api\Command;

use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\CaWebexBundle\Api\Command\CreateRegistrantCommand;
use MauticPlugin\CaWebexBundle\Api\WebexApi;
use MauticPlugin\CaWebexBundle\DataObject\WebexResponseDto;
use MauticPlugin\CaWebexBundle\Helper\WebexIntegrationHelper;
use PHPUnit\Framework\TestCase;

class CreateRegistrantCommandTest extends TestCase
{
    public function testExecuteCallsApiRequestWithCorrectParameters(): void
    {
        $lead = new Lead();
        $lead->setEmail('test@example.com');
        $lead->setFirstname('John');
        $lead->setLastname('Doe');
        $meetingId   = 'meeting123';

        $apiMock = $this->createMock(WebexApi::class);
        $apiMock->expects($this->once())
            ->method('request')
            ->with("/meetings/{$meetingId}/registrants", [
                'email'       => $lead->getEmail(),
                'firstName' => $lead->getFirstname(),
                'lastName' => $lead->getLastname(),
            ], 'POST')
            ->willReturn(new WebexResponseDto(200, []));

        $webexIntegrationHelperMock = $this->createMock(WebexIntegrationHelper::class);
        $webexIntegrationHelperMock->method('getApi')->willReturn($apiMock);

        $command = new CreateRegistrantCommand($webexIntegrationHelperMock);
        $command->execute($meetingId, $lead);
    }

    public function testExecuteReturnsResponseBody(): void
    {
        $lead = new Lead();
        $lead->setEmail('test@example.com');
        $lead->setFirstname('John');
        $lead->setLastname('Doe');
        $meetingId = 'meeting123';
        $responseBody = [
            'id' => '118c7202-0686-4c7a-9c8d-ddaec9918e68',
            'status' => 'approved',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'test@example.com',
            'registrationTime' => '2023-11-17T10:39:44+01:00',
            'registrationId' => '692687'
        ];

        $responseMock = $this->createMock(WebexResponseDto::class);
        $responseMock->method('getBody')->willReturn($responseBody);

        $apiMock = $this->createMock(WebexApi::class);
        $apiMock->method('request')->willReturn($responseMock);

        $apiHelperMock = $this->createMock(WebexIntegrationHelper::class);
        $apiHelperMock->method('getApi')->willReturn($apiMock);

        $command = new CreateRegistrantCommand($apiHelperMock);
        $result = $command->execute($meetingId, $lead);

        $this->assertEquals($responseBody, $result);
    }
}
