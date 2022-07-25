<?php

namespace Redmine\Test;

use Http\Mock\Client;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Redmine\ApiClient;
use Redmine\Exception\BadRequestException;
use Redmine\Exception\InternalServerErrorException;

class ApiCT extends TestCase
{
    public function testGet(): void
    {
        $client = new Client();

        $redmine = new ApiClient(
            $client,
            new Psr17Factory(),
            new Psr17Factory(),
            'http://redmine.example.test',
            'test-api-key'
        );
        $httpResponseStream = $this->createMock(StreamInterface::class);
        $httpResponseStream->method('getContents')->willReturn('{"foo":"bar"}');
        $httpResponse = $this->createMock(ResponseInterface::class);
        $httpResponse->method('getBody')->willReturn($httpResponseStream);
        $httpResponse->method('getStatusCode')->willReturn(200);
        $client->addResponse($httpResponse);

        $apiResponse = $redmine->requestGet('pages/test', [
            'foo' => ['bar', 'baz'],
        ]);


        $requests = $client->getRequests();
        $this->assertCount(1, $requests);

        $request = $requests[0];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('application/json', $request->getHeaderLine('Content-type'));
        $this->assertEquals('test-api-key', $request->getHeaderLine('X-Redmine-API-Key'));
        $this->assertEquals(
            'http://redmine.example.test/pages/test.json?foo%5B0%5D=bar&foo%5B1%5D=baz',
            $request->getUri()->__toString()
        );

        $this->assertEquals(200, $apiResponse->getStatusCode());
        $this->assertEquals('bar', $apiResponse['foo']);
    }

    public function testDelete(): void
    {
        $client = new Client();

        $redmine = new ApiClient(
            $client,
            new Psr17Factory(),
            new Psr17Factory(),
            'http://redmine.example.test',
            'test-api-key'
        );
        $httpResponseStream = $this->createMock(StreamInterface::class);
        $httpResponseStream->method('getContents')->willReturn('{"foo":"bar"}');
        $httpResponse = $this->createMock(ResponseInterface::class);
        $httpResponse->method('getBody')->willReturn($httpResponseStream);
        $httpResponse->method('getStatusCode')->willReturn(200);
        $client->addResponse($httpResponse);

        $apiResponse = $redmine->requestDelete('pages/test', [
            'foo' => ['bar', 'baz'],
        ]);

        $requests = $client->getRequests();
        $this->assertCount(1, $requests);

        $request = $requests[0];
        $this->assertEquals('DELETE', $request->getMethod());
        $this->assertEquals('application/json', $request->getHeaderLine('Content-type'));
        $this->assertEquals('test-api-key', $request->getHeaderLine('X-Redmine-API-Key'));
        $this->assertEquals(
            'http://redmine.example.test/pages/test.json?foo%5B0%5D=bar&foo%5B1%5D=baz',
            $request->getUri()->__toString()
        );

        $this->assertEquals(200, $apiResponse->getStatusCode());
        $this->assertEquals('bar', $apiResponse['foo']);
    }

    public function testPut(): void
    {
        $client = new Client();

        $redmine = new ApiClient(
            $client,
            new Psr17Factory(),
            new Psr17Factory(),
            'http://redmine.example.test',
            'test-api-key'
        );
        $httpResponseStream = $this->createMock(StreamInterface::class);
        $httpResponseStream->method('getContents')->willReturn('{"foo":"bar"}');
        $httpResponse = $this->createMock(ResponseInterface::class);
        $httpResponse->method('getBody')->willReturn($httpResponseStream);
        $httpResponse->method('getStatusCode')->willReturn(200);
        $client->addResponse($httpResponse);

        $apiResponse = $redmine->requestPut('pages/test', [
            'key' => [
                'value1',
                'value2',
            ],
        ], [
            'foo' => ['bar', 'baz'],
        ]);

        $requests = $client->getRequests();
        $this->assertCount(1, $requests);

        $request = $requests[0];
        $this->assertEquals('PUT', $request->getMethod());
        $this->assertEquals('application/json', $request->getHeaderLine('Content-type'));
        $this->assertEquals('test-api-key', $request->getHeaderLine('X-Redmine-API-Key'));
        $this->assertEquals(
            'http://redmine.example.test/pages/test.json?foo%5B0%5D=bar&foo%5B1%5D=baz',
            $request->getUri()->__toString()
        );

        $request->getBody()->rewind();
        $this->assertEquals(
            '{"key":["value1","value2"]}',
            $request->getBody()->getContents(),
        );

        $this->assertEquals(200, $apiResponse->getStatusCode());
        $this->assertEquals('bar', $apiResponse['foo']);
    }

    public function testPost(): void
    {
        $client = new Client();

        $redmine = new ApiClient(
            $client,
            new Psr17Factory(),
            new Psr17Factory(),
            'http://redmine.example.test',
            'test-api-key'
        );
        $httpResponseStream = $this->createMock(StreamInterface::class);
        $httpResponseStream->method('getContents')->willReturn('{"foo":"bar"}');
        $httpResponse = $this->createMock(ResponseInterface::class);
        $httpResponse->method('getBody')->willReturn($httpResponseStream);
        $httpResponse->method('getStatusCode')->willReturn(200);
        $client->addResponse($httpResponse);

        $apiResponse = $redmine->requestPost('pages/test', [
            'key' => [
                'value1',
                'value2',
            ],
        ], [
            'foo' => ['bar', 'baz'],
        ]);

        $requests = $client->getRequests();
        $this->assertCount(1, $requests);

        $request = $requests[0];
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('application/json', $request->getHeaderLine('Content-type'));
        $this->assertEquals('test-api-key', $request->getHeaderLine('X-Redmine-API-Key'));
        $this->assertEquals(
            'http://redmine.example.test/pages/test.json?foo%5B0%5D=bar&foo%5B1%5D=baz',
            $request->getUri()->__toString()
        );

        $request->getBody()->rewind();
        $this->assertEquals(
            '{"key":["value1","value2"]}',
            $request->getBody()->getContents(),
        );

        $this->assertEquals(200, $apiResponse->getStatusCode());
        $this->assertEquals('bar', $apiResponse['foo']);
    }

    public function testThrowsBadRequestException(): void
    {
        $client = new Client();

        $redmine = new ApiClient(
            $client,
            new Psr17Factory(),
            new Psr17Factory(),
            'http://redmine.example.test',
            'test-api-key'
        );
        $httpResponseStream = $this->createMock(StreamInterface::class);
        $httpResponseStream->method('getContents')->willReturn('{"error":"not found"}');
        $httpResponse = $this->createMock(ResponseInterface::class);
        $httpResponse->method('getBody')->willReturn($httpResponseStream);
        $httpResponse->method('getStatusCode')->willReturn(404);
        $client->addResponse($httpResponse);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Error in redmine api request GET http://redmine.example.test/pages/test.json?foo%5B0%5D=bar&foo%5B1%5D=baz: {"error":"not found"}');
        $redmine->requestGet('pages/test', [
            'foo' => ['bar', 'baz'],
        ]);

        $requests = $client->getRequests();
        $this->assertCount(1, $requests);

        $request = $requests[0];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('application/json', $request->getHeaderLine('Content-type'));
        $this->assertEquals('test-api-key', $request->getHeaderLine('X-Redmine-API-Key'));
        $this->assertEquals(
            'http://redmine.example.test/pages/test.json?foo%5B0%5D=bar&foo%5B1%5D=baz',
            $request->getUri()->__toString()
        );
    }
    public function testThrowsInternalServerErrorException(): void
    {
        $client = new Client();

        $redmine = new ApiClient(
            $client,
            new Psr17Factory(),
            new Psr17Factory(),
            'http://redmine.example.test',
            'test-api-key'
        );
        $httpResponse = $this->createMock(ResponseInterface::class);
        $httpResponse->method('getStatusCode')->willReturn(500);
        $client->addResponse($httpResponse);

        $this->expectException(InternalServerErrorException::class);
        $this->expectExceptionMessage('Error in redmine api on GET http://redmine.example.test/pages/test.json?foo%5B0%5D=bar&foo%5B1%5D=baz');
        $redmine->requestGet('pages/test', [
            'foo' => ['bar', 'baz'],
        ]);

        $requests = $client->getRequests();
        $this->assertCount(1, $requests);

        $request = $requests[0];
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('application/json', $request->getHeaderLine('Content-type'));
        $this->assertEquals('test-api-key', $request->getHeaderLine('X-Redmine-API-Key'));
        $this->assertEquals(
            'http://redmine.example.test/pages/test.json?foo%5B0%5D=bar&foo%5B1%5D=baz',
            $request->getUri()->__toString()
        );
    }
}
