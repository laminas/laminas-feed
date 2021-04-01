<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\PubSubHubbub;

use Laminas\Feed\PubSubHubbub\Exception\ExceptionInterface;
use Laminas\Feed\PubSubHubbub\Publisher;
use Laminas\Feed\PubSubHubbub\PubSubHubbub;
use Laminas\Http\Client as HttpClient;
use Laminas\Http\Response as HttpResponse;
use PHPUnit\Framework\TestCase;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Subsubhubbub
 */
class PublisherTest extends TestCase
{
    /** @var Publisher */
    protected $publisher;

    protected function setUp(): void
    {
        $client = new HttpClient();
        PubSubHubbub::setHttpClient($client);
        $this->publisher = new Publisher();
    }

    public function getClientSuccess(): ClientNotReset
    {
        $response = new HttpResponse();
        $response->setStatusCode(204);

        $client = new ClientNotReset();
        $client->setResponse($response);

        return $client;
    }

    public function getClientFail(): ClientNotReset
    {
        $response = new HttpResponse();
        $response->setStatusCode(404);

        $client = new ClientNotReset();
        $client->setResponse($response);

        return $client;
    }

    public function testAddsHubServerUrl(): void
    {
        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->assertEquals(['http://www.example.com/hub'], $this->publisher->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArray(): void
    {
        $this->publisher->addHubUrls([
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
        ]);
        $this->assertEquals([
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
        ], $this->publisher->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArrayUsingSetConfig(): void
    {
        $this->publisher->setOptions([
            'hubUrls' => [
                'http://www.example.com/hub',
                'http://www.example.com/hub2',
            ],
        ]);
        $this->assertEquals([
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
        ], $this->publisher->getHubUrls());
    }

    public function testRemovesHubServerUrl(): void
    {
        $this->publisher->addHubUrls([
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
        ]);
        $this->publisher->removeHubUrl('http://www.example.com/hub');
        $this->assertEquals([
            1 => 'http://www.example.com/hub2',
        ], $this->publisher->getHubUrls());
    }

    public function testRetrievesUniqueHubServerUrlsOnly(): void
    {
        $this->publisher->addHubUrls([
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
            'http://www.example.com/hub',
        ]);
        $this->assertEquals([
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
        ], $this->publisher->getHubUrls());
    }

    public function testThrowsExceptionOnSettingEmptyHubServerUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->publisher->addHubUrl('');
    }

    public function testThrowsExceptionOnSettingNonStringHubServerUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->publisher->addHubUrl(123);
    }

    public function testThrowsExceptionOnSettingInvalidHubServerUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->publisher->addHubUrl('http://');
    }

    public function testAddsUpdatedTopicUrl(): void
    {
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->assertEquals(['http://www.example.com/topic'], $this->publisher->getUpdatedTopicUrls());
    }

    public function testAddsUpdatedTopicUrlsFromArray(): void
    {
        $this->publisher->addUpdatedTopicUrls([
            'http://www.example.com/topic',
            'http://www.example.com/topic2',
        ]);
        $this->assertEquals([
            'http://www.example.com/topic',
            'http://www.example.com/topic2',
        ], $this->publisher->getUpdatedTopicUrls());
    }

    public function testAddsUpdatedTopicUrlsFromArrayUsingSetConfig(): void
    {
        $this->publisher->setOptions([
            'updatedTopicUrls' => [
                'http://www.example.com/topic',
                'http://www.example.com/topic2',
            ],
        ]);
        $this->assertEquals([
            'http://www.example.com/topic',
            'http://www.example.com/topic2',
        ], $this->publisher->getUpdatedTopicUrls());
    }

    public function testRemovesUpdatedTopicUrl(): void
    {
        $this->publisher->addUpdatedTopicUrls([
            'http://www.example.com/topic',
            'http://www.example.com/topic2',
        ]);
        $this->publisher->removeUpdatedTopicUrl('http://www.example.com/topic');
        $this->assertEquals([
            1 => 'http://www.example.com/topic2',
        ], $this->publisher->getUpdatedTopicUrls());
    }

    public function testRetrievesUniqueUpdatedTopicUrlsOnly(): void
    {
        $this->publisher->addUpdatedTopicUrls([
            'http://www.example.com/topic',
            'http://www.example.com/topic2',
            'http://www.example.com/topic',
        ]);
        $this->assertEquals([
            'http://www.example.com/topic',
            'http://www.example.com/topic2',
        ], $this->publisher->getUpdatedTopicUrls());
    }

    public function testThrowsExceptionOnSettingEmptyUpdatedTopicUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->publisher->addUpdatedTopicUrl('');
    }

    public function testThrowsExceptionOnSettingNonStringUpdatedTopicUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->publisher->addUpdatedTopicUrl(123);
    }

    public function testThrowsExceptionOnSettingInvalidUpdatedTopicUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->publisher->addUpdatedTopicUrl('http://');
    }

    public function testAddsParameter(): void
    {
        $this->publisher->setParameter('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $this->publisher->getParameters());
    }

    public function testAddsParametersFromArray(): void
    {
        $this->publisher->setParameters([
            'foo' => 'bar',
            'boo' => 'baz',
        ]);
        $this->assertEquals([
            'foo' => 'bar',
            'boo' => 'baz',
        ], $this->publisher->getParameters());
    }

    public function testAddsParametersFromArrayInSingleMethod(): void
    {
        $this->publisher->setParameter([
            'foo' => 'bar',
            'boo' => 'baz',
        ]);
        $this->assertEquals([
            'foo' => 'bar',
            'boo' => 'baz',
        ], $this->publisher->getParameters());
    }

    public function testAddsParametersFromArrayUsingSetConfig(): void
    {
        $this->publisher->setOptions([
            'parameters' => [
                'foo' => 'bar',
                'boo' => 'baz',
            ],
        ]);
        $this->assertEquals([
            'foo' => 'bar',
            'boo' => 'baz',
        ], $this->publisher->getParameters());
    }

    public function testRemovesParameter(): void
    {
        $this->publisher->setParameters([
            'foo' => 'bar',
            'boo' => 'baz',
        ]);
        $this->publisher->removeParameter('boo');
        $this->assertEquals([
            'foo' => 'bar',
        ], $this->publisher->getParameters());
    }

    public function testRemovesParameterIfSetToNull(): void
    {
        $this->publisher->setParameters([
            'foo' => 'bar',
            'boo' => 'baz',
        ]);
        $this->publisher->setParameter('boo', null);
        $this->assertEquals([
            'foo' => 'bar',
        ], $this->publisher->getParameters());
    }

    public function testNotifiesHubWithCorrectParameters(): void
    {
        PubSubHubbub::setHttpClient($this->getClientSuccess());
        $client = PubSubHubbub::getHttpClient();
        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->publisher->setParameter('foo', 'bar');
        $this->publisher->notifyAll();
        $this->assertEquals(
            'hub.mode=publish&hub.url=http%3A%2F%2Fwww.example.com%2Ftopic&foo=bar',
            $client->getRequest()->getContent()
        );
    }

    public function testNotifiesHubWithCorrectParametersAndMultipleTopics(): void
    {
        PubSubHubbub::setHttpClient($this->getClientSuccess());
        $client = PubSubHubbub::getHttpClient();
        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic2');
        $this->publisher->notifyAll();
        $this->assertEquals(
            'hub.mode=publish&hub.url=http%3A%2F%2Fwww.example.com%2Ftopic&'
            . 'hub.url=http%3A%2F%2Fwww.example.com%2Ftopic2',
            $client->getRequest()->getContent()
        );
    }

    public function testNotifiesHubAndReportsSuccess(): void
    {
        PubSubHubbub::setHttpClient($this->getClientSuccess());
        $client = PubSubHubbub::getHttpClient();
        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->publisher->setParameter('foo', 'bar');
        $this->publisher->notifyAll();
        $this->assertTrue($this->publisher->isSuccess());
    }

    public function testNotifiesHubAndReportsFail(): void
    {
        PubSubHubbub::setHttpClient($this->getClientFail());
        $client = PubSubHubbub::getHttpClient();
        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->publisher->setParameter('foo', 'bar');
        $this->publisher->notifyAll();
        $this->assertFalse($this->publisher->isSuccess());
    }

    public function testNotifyAllSendsRequestViaClient(): void
    {
        $response = $this->createMock(HttpResponse::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn(204);

        $client = $this->createMock(HttpClient::class);
        $client
            ->expects($this->once())
            ->method('setUri')
            ->with('http://www.example.com/hub');
        $client
            ->expects($this->once())
            ->method('send');
        $client
            ->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);

        PubSubHubbub::setHttpClient($client);

        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->publisher->setParameter('foo', 'bar');
        $this->publisher->notifyAll();
        $this->assertTrue($this->publisher->isSuccess());
    }
}
