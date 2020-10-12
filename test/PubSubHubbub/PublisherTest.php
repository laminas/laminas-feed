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

    public function getClientSuccess()
    {
        $response = new HttpResponse();
        $response->setStatusCode(204);

        $client = new ClientNotReset();
        $client->setResponse($response);

        return $client;
    }

    public function getClientFail()
    {
        $response = new HttpResponse();
        $response->setStatusCode(404);

        $client = new ClientNotReset();
        $client->setResponse($response);

        return $client;
    }

    public function testAddsHubServerUrl()
    {
        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->assertEquals(['http://www.example.com/hub'], $this->publisher->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArray()
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

    public function testAddsHubServerUrlsFromArrayUsingSetConfig()
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

    public function testRemovesHubServerUrl()
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

    public function testRetrievesUniqueHubServerUrlsOnly()
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

    public function testThrowsExceptionOnSettingEmptyHubServerUrl()
    {
        $this->expectException(ExceptionInterface::class);
        $this->publisher->addHubUrl('');
    }

    public function testThrowsExceptionOnSettingNonStringHubServerUrl()
    {
        $this->expectException(ExceptionInterface::class);
        $this->publisher->addHubUrl(123);
    }

    public function testThrowsExceptionOnSettingInvalidHubServerUrl()
    {
        $this->expectException(ExceptionInterface::class);
        $this->publisher->addHubUrl('http://');
    }

    public function testAddsUpdatedTopicUrl()
    {
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->assertEquals(['http://www.example.com/topic'], $this->publisher->getUpdatedTopicUrls());
    }

    public function testAddsUpdatedTopicUrlsFromArray()
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

    public function testAddsUpdatedTopicUrlsFromArrayUsingSetConfig()
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

    public function testRemovesUpdatedTopicUrl()
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

    public function testRetrievesUniqueUpdatedTopicUrlsOnly()
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

    public function testThrowsExceptionOnSettingEmptyUpdatedTopicUrl()
    {
        $this->expectException(ExceptionInterface::class);
        $this->publisher->addUpdatedTopicUrl('');
    }

    public function testThrowsExceptionOnSettingNonStringUpdatedTopicUrl()
    {
        $this->expectException(ExceptionInterface::class);
        $this->publisher->addUpdatedTopicUrl(123);
    }

    public function testThrowsExceptionOnSettingInvalidUpdatedTopicUrl()
    {
        $this->expectException(ExceptionInterface::class);
        $this->publisher->addUpdatedTopicUrl('http://');
    }

    public function testAddsParameter()
    {
        $this->publisher->setParameter('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $this->publisher->getParameters());
    }

    public function testAddsParametersFromArray()
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

    public function testAddsParametersFromArrayInSingleMethod()
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

    public function testAddsParametersFromArrayUsingSetConfig()
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

    public function testRemovesParameter()
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

    public function testRemovesParameterIfSetToNull()
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

    public function testNotifiesHubWithCorrectParameters()
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

    public function testNotifiesHubWithCorrectParametersAndMultipleTopics()
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

    public function testNotifiesHubAndReportsSuccess()
    {
        PubSubHubbub::setHttpClient($this->getClientSuccess());
        $client = PubSubHubbub::getHttpClient();
        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->publisher->setParameter('foo', 'bar');
        $this->publisher->notifyAll();
        $this->assertTrue($this->publisher->isSuccess());
    }

    public function testNotifiesHubAndReportsFail()
    {
        PubSubHubbub::setHttpClient($this->getClientFail());
        $client = PubSubHubbub::getHttpClient();
        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->publisher->setParameter('foo', 'bar');
        $this->publisher->notifyAll();
        $this->assertFalse($this->publisher->isSuccess());
    }
}
