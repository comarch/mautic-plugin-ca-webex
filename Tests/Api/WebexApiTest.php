<?php

namespace MauticPlugin\CaWebexBundle\Tests\Api;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Mautic\PluginBundle\Exception\ApiErrorException;
use MauticPlugin\CaWebexBundle\Api\WebexApi;
use MauticPlugin\CaWebexBundle\Integration\WebexIntegration;
use PHPUnit\Framework\TestCase;

class WebexApiTest extends TestCase
{
    public function testRequestReturnsWebexResponseDtoOnSuccess(): void
    {
        $streamMock = $this->createMock(Stream::class);
        $streamMock->method('__toString')->willReturn('[]');
        $responseMock = $this->createMock(Response::class);
        $responseMock->method('getBody')->willReturn($streamMock);
        $responseMock->method('getStatusCode')->willReturn(200);
        $integrationMock = $this->createMock(WebexIntegration::class);
        $integrationMock->method('makeRequest')->willReturn($responseMock);

        $api    = new WebexApi($integrationMock);
        $result = $api->request('/endpoint');
        $this->assertSame([], $result->getBody());
    }

    public function testRequestHandlesErrorResponse(): void
    {
        $integrationMock = $this->createMock(WebexIntegration::class);
        $integrationMock->method('makeRequest')->willReturn(['error' => ['code' => 500, 'message' => 'Internal Server Error']]);

        $api = new WebexApi($integrationMock);

        $this->expectException(ApiErrorException::class);
        $api->request('/endpoint');
    }

    public function testRequestThrowsApiErrorExceptionForInvalidResponse(): void
    {
        $integrationMock = $this->createMock(WebexIntegration::class);
        $integrationMock->method('makeRequest')->willReturn('Invalid response');

        $api = new WebexApi($integrationMock);

        $this->expectException(ApiErrorException::class);
        $api->request('/endpoint');
    }
}
