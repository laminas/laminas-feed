<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Reader\Http;

use Laminas\Feed\Reader\Http\HeaderAwareResponseInterface;
use Laminas\Feed\Reader\Http\Psr7ResponseDecorator;
use Laminas\Feed\Reader\Http\ResponseInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @covers \Laminas\Feed\Reader\Http\Psr7ResponseDecorator
 */
class Psr7ResponseDecoratorTest extends TestCase
{
    public function testDecoratorIsAFeedResponse(): void
    {
        $originalResponse = $this->createMock(Psr7ResponseInterface::class);
        $decorator        = new Psr7ResponseDecorator($originalResponse);
        $this->assertInstanceOf(ResponseInterface::class, $decorator);
    }

    public function testDecoratorIsAHeaderAwareResponse(): void
    {
        $originalResponse = $this->createMock(Psr7ResponseInterface::class);
        $decorator        = new Psr7ResponseDecorator($originalResponse);
        $this->assertInstanceOf(HeaderAwareResponseInterface::class, $decorator);
    }

    public function testDecoratorIsNotAPsr7Response(): void
    {
        $originalResponse = $this->createMock(Psr7ResponseInterface::class);
        $decorator        = new Psr7ResponseDecorator($originalResponse);
        $this->assertNotInstanceOf(Psr7ResponseInterface::class, $decorator);
    }

    public function testCanRetrieveDecoratedResponse(): void
    {
        $originalResponse = $this->createMock(Psr7ResponseInterface::class);
        $decorator        = new Psr7ResponseDecorator($originalResponse);
        $this->assertSame($originalResponse, $decorator->getDecoratedResponse());
    }

    public function testProxiesToDecoratedResponseToRetrieveStatusCode(): void
    {
        $originalResponse = $this->createMock(Psr7ResponseInterface::class);
        $originalResponse
            ->method('getStatusCode')
            ->willReturn(301);
        $decorator = new Psr7ResponseDecorator($originalResponse);
        $this->assertSame(301, $decorator->getStatusCode());
    }

    public function testProxiesToDecoratedResponseToRetrieveBody(): void
    {
        $body = $this->createMock(StreamInterface::class);
        $body->method('__toString')
            ->willReturn('BODY');
        $originalResponse = $this->createMock(Psr7ResponseInterface::class);
        $originalResponse
            ->method('getBody')
            ->willReturn($body);
        $decorator = new Psr7ResponseDecorator($originalResponse);
        $this->assertSame('BODY', $decorator->getBody());
    }

    public function testProxiesToDecoratedResponseToRetrieveHeaderLine(): void
    {
        $originalResponse = $this->createMock(Psr7ResponseInterface::class);
        $originalResponse
            ->method('hasHeader')
            ->with('E-Tag')
            ->willReturn(true);

        $originalResponse
            ->method('getHeaderLine')
            ->with('E-Tag')
            ->willReturn('2015-11-17 12:32:00-06:00');
        $decorator = new Psr7ResponseDecorator($originalResponse);
        $this->assertSame('2015-11-17 12:32:00-06:00', $decorator->getHeaderLine('E-Tag'));
    }

    public function testDecoratorReturnsDefaultValueWhenOriginalResponseDoesNotHaveHeader(): void
    {
        $originalResponse = $this->createMock(Psr7ResponseInterface::class);
        $originalResponse
            ->method('hasHeader')
            ->with('E-Tag')
            ->willReturn(false);

        $originalResponse
            ->expects($this->never())
            ->method('getHeaderLine')
            ->with('E-Tag');

        $decorator = new Psr7ResponseDecorator($originalResponse);
        $this->assertSame('2015-11-17 12:32:00-06:00', $decorator->getHeaderLine('E-Tag', '2015-11-17 12:32:00-06:00'));
    }
}
