<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Reader\Http;

use Laminas\Feed\Reader\Exception\InvalidArgumentException;
use Laminas\Feed\Reader\Http\LaminasHttpClientDecorator;
use Laminas\Feed\Reader\Http\Response as FeedResponse;
use Laminas\Http\Client;
use Laminas\Http\Headers;
use Laminas\Http\Request as HttpRequest;
use Laminas\Http\Response as HttpResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Laminas\Feed\Reader\Http\LaminasHttpClientDecorator
 */
class LaminasHttpClientDecoratorTest extends TestCase
{
    /**
     * @var Client|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $client;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
    }

    public function prepareDefaultClientInteractions($uri, MockObject $response): void
    {
        $this->client
            ->expects($this->atLeastOnce())
            ->method('resetParameters');

        $this->client
            ->expects($this->atLeastOnce())
            ->method('setMethod')
            ->with('GET');

        $this->client
            ->expects($this->atLeastOnce())
            ->method('setHeaders')
            ->with($this->callback(static function ($parameter): bool {
                self::assertInstanceOf(Headers::class, $parameter);
                return true;
            }));

        $this->client
            ->expects($this->atLeastOnce())
            ->method('setUri')
            ->with($uri);

        $this->client
            ->expects($this->once())
            ->method('send')
            ->willReturn($response);
    }

    /**
     * @return MockObject
     *
     * @psalm-return MockObject<HttpResponse>
     */
    public function createMockHttpResponse(int $statusCode, string $body, Headers $headers = null): MockObject
    {
        $response = $this->createMock(HttpResponse::class);
        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn($statusCode);

        $response
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($body);
        $response
            ->expects($this->any())
            ->method('getHeaders')
            ->willReturn($headers ?? new Headers());

        return $response;
    }

    /**
     * @param array $headers
     *
     * @return MockObject<Headers>
     */
    public function createMockHttpHeaders(array $headers): Headers
    {
        $mock = $this->createMock(Headers::class);
        $mock
            ->expects($this->any())
            ->method('toArray')
            ->willReturn($headers);

        return $mock;
    }

    public function testProvidesAccessToDecoratedClient(): void
    {
        $client    = $this->createMock(Client::class);
        $decorator = new LaminasHttpClientDecorator($client);
        $this->assertSame($client, $decorator->getDecoratedClient());
    }

    public function testDecoratorReturnsFeedResponse(): void
    {
        $headers      = $this->createMockHttpHeaders(['Content-Type' => 'application/rss+xml']);
        $httpResponse = $this->createMockHttpResponse(200, '', $headers);
        $this->prepareDefaultClientInteractions('http://example.com', $httpResponse);

        $client   = new LaminasHttpClientDecorator($this->client);
        $response = $client->get('http://example.com');

        $this->assertInstanceOf(FeedResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('', $response->getBody());
        $this->assertEquals('application/rss+xml', $response->getHeaderLine('Content-Type'));
    }

    public function testDecoratorInjectsProvidedHeadersIntoClientWhenSending(): void
    {
        $responseHeaders = $this->createMockHttpHeaders([
            'Content-Type'     => 'application/rss+xml',
            'Content-Length'   => 1234,
            'X-Content-Length' => 1234.56,
        ]);
        $httpResponse    = $this->createMockHttpResponse(200, '', $responseHeaders);
        $this->prepareDefaultClientInteractions('http://example.com', $httpResponse);

        $requestHeaders = $this->createMock(Headers::class);
        $requestHeaders
            ->expects($this->atLeastOnce())
            ->method('addHeaderLine')
            ->with('Accept', 'application/rss+xml');

        $request = $this->createMock(HttpRequest::class);
        $request
            ->expects($this->any())
            ->method('getHeaders')
            ->willReturn($requestHeaders);
        $this->client
            ->expects($this->any())
            ->method('getRequest')
            ->willReturn($request);

        $client   = new LaminasHttpClientDecorator($this->client);
        $response = $client->get('http://example.com', ['Accept' => ['application/rss+xml']]);

        $this->assertInstanceOf(FeedResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('', $response->getBody());
        $this->assertEquals('application/rss+xml', $response->getHeaderLine('Content-Type'));
        $this->assertEquals(1234, $response->getHeaderLine('Content-Length'));
        $this->assertEquals(1234.56, $response->getHeaderLine('X-Content-Length'));
    }

    public function invalidHeaders(): \Generator
    {
        $basicTests = [
            'zero-name'        => [
                [['value']],
                'Header names',
            ],
            'int-name'         => [
                [1 => ['value']],
                'Header names',
            ],
            'numeric-name'     => [
                ['1.1' => ['value']],
                'Header names',
            ],
            'empty-name'       => [
                ['' => ['value']],
                'Header names',
            ],
            'null-value'       => [
                ['X-Test' => null],
                'Header values',
            ],
            'true-value'       => [
                ['X-Test' => true],
                'Header values',
            ],
            'false-value'      => [
                ['X-Test' => false],
                'Header values',
            ],
            'zero-value'       => [
                ['X-Test' => 0],
                'Header values',
            ],
            'int-value'        => [
                ['X-Test' => 1],
                'Header values',
            ],
            'zero-float-value' => [
                ['X-Test' => 0.0],
                'Header values',
            ],
            'float-value'      => [
                ['X-Test' => 1.1],
                'Header values',
            ],
            'string-value'     => [
                ['X-Test' => 'value'],
                'Header values',
            ],
            'object-value'     => [
                ['X-Test' => (object) ['value']],
                'Header values',
            ],
        ];

        foreach ($basicTests as $key => $arguments) {
            yield $key => $arguments;
        }

        $invalidIndividualValues = [
            'null-individual-value'   => null,
            'true-individual-value'   => true,
            'false-individual-value'  => false,
            'array-individual-value'  => ['string'],
            'object-individual-value' => (object) ['string'],
        ];

        foreach ($invalidIndividualValues as $key => $value) {
            yield $key => [['X-Test' => [$value]], 'strings or numbers'];
        }
    }

    /**
     * @psalm-param array<array-key,string> $headers
     * @param string $contains
     * @dataProvider invalidHeaders
     */
    public function testDecoratorRaisesExceptionForInvalidHeaders($headers, $contains): void
    {
        $this->client
            ->expects($this->atLeastOnce())
            ->method('resetParameters');
        $this->client
            ->expects($this->atLeastOnce())
            ->method('setMethod')
            ->with('GET');
        $this->client
            ->expects($this->atLeastOnce())
            ->method('setHeaders')
            ->with($this->callback(static function ($argument): bool {
                self::assertInstanceOf(Headers::class, $argument);
                return true;
            }));

        $this->client
            ->expects($this->atLeastOnce())
            ->method('setUri')
            ->with('http://example.com');

        $requestHeaders = $this->createMock(Headers::class);
        $request        = $this->createMock(HttpRequest::class);
        $request
            ->expects($this->any())
            ->method('getHeaders')
            ->willReturn($requestHeaders);
        $this->client
            ->expects($this->any())
            ->method('getRequest')
            ->willReturn($request);

        $client = new LaminasHttpClientDecorator($this->client);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($contains);
        $client->get('http://example.com', $headers);
    }
}
