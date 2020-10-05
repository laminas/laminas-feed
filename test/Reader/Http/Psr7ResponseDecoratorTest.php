<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Reader\Http;

use Laminas\Feed\Reader\Http\HeaderAwareResponseInterface;
use Laminas\Feed\Reader\Http\Psr7ResponseDecorator;
use Laminas\Feed\Reader\Http\ResponseInterface;
use LaminasTest\Feed\Reader\TestAsset\Psr7Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

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
        $originalResponse = $this->createMock(Psr7ResponseInterface::class);
        $originalResponse
            ->method('getBody')
            ->willReturn('BODY');
        $decorator = new Psr7ResponseDecorator($originalResponse);
        $this->assertSame('BODY', $decorator->getBody());
    }

    public function testCastsStreamToStringWhenReturningPsr7Body(): void
    {
        $stream           = new Psr7Stream('BODY');
        $originalResponse = $this->createMock(Psr7ResponseInterface::class);
        $originalResponse
            ->method('getBody')
            ->willReturn($stream);
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
