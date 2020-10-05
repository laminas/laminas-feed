<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\PubSubHubbub;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Feed\PubSubHubbub\Exception\ExceptionInterface;
use Laminas\Feed\PubSubHubbub\Model\Subscription;
use Laminas\Feed\PubSubHubbub\PubSubHubbub;
use Laminas\Feed\PubSubHubbub\Subscriber;
use Laminas\Http\Client as HttpClient;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Subsubhubbub
 */
class SubscriberTest extends TestCase
{
    /** @var Subscriber */
    protected $subscriber;

    protected $adapter;

    protected $tableGateway;

    protected function setUp(): void
    {
        $client = new HttpClient();
        PubSubHubbub::setHttpClient($client);
        $this->subscriber   = new Subscriber();
        $this->adapter      = $this->_getCleanMock(
            Adapter::class
        );
        $this->tableGateway = $this->_getCleanMock(
            TableGateway::class
        );
        $this->tableGateway->expects($this->any())->method('getAdapter')
            ->will($this->returnValue($this->adapter));
    }

    public function testAddsHubServerUrl(): void
    {
        $this->subscriber->addHubUrl('http://www.example.com/hub');
        $this->assertEquals(['http://www.example.com/hub'], $this->subscriber->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArray(): void
    {
        $this->subscriber->addHubUrls([
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
        ]);
        $this->assertEquals([
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
        ], $this->subscriber->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArrayUsingSetOptions(): void
    {
        $this->subscriber->setOptions([
            'hubUrls' => [
                'http://www.example.com/hub',
                'http://www.example.com/hub2',
            ],
        ]);
        $this->assertEquals([
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
        ], $this->subscriber->getHubUrls());
    }

    public function testRemovesHubServerUrl(): void
    {
        $this->subscriber->addHubUrls([
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
        ]);
        $this->subscriber->removeHubUrl('http://www.example.com/hub');
        $this->assertEquals([
            1 => 'http://www.example.com/hub2',
        ], $this->subscriber->getHubUrls());
    }

    public function testRetrievesUniqueHubServerUrlsOnly(): void
    {
        $this->subscriber->addHubUrls([
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
            'http://www.example.com/hub',
        ]);
        $this->assertEquals([
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
        ], $this->subscriber->getHubUrls());
    }

    public function testThrowsExceptionOnSettingEmptyHubServerUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->addHubUrl('');
    }

    public function testThrowsExceptionOnSettingNonStringHubServerUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->addHubUrl(123);
    }

    public function testThrowsExceptionOnSettingInvalidHubServerUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->addHubUrl('http://');
    }

    public function testAddsParameter(): void
    {
        $this->subscriber->setParameter('foo', 'bar');
        $this->assertEquals(['foo' => 'bar'], $this->subscriber->getParameters());
    }

    public function testAddsParametersFromArray(): void
    {
        $this->subscriber->setParameters([
            'foo' => 'bar',
            'boo' => 'baz',
        ]);
        $this->assertEquals([
            'foo' => 'bar',
            'boo' => 'baz',
        ], $this->subscriber->getParameters());
    }

    public function testAddsParametersFromArrayInSingleMethod(): void
    {
        $this->subscriber->setParameter([
            'foo' => 'bar',
            'boo' => 'baz',
        ]);
        $this->assertEquals([
            'foo' => 'bar',
            'boo' => 'baz',
        ], $this->subscriber->getParameters());
    }

    public function testAddsParametersFromArrayUsingSetOptions(): void
    {
        $this->subscriber->setOptions([
            'parameters' => [
                'foo' => 'bar',
                'boo' => 'baz',
            ],
        ]);
        $this->assertEquals([
            'foo' => 'bar',
            'boo' => 'baz',
        ], $this->subscriber->getParameters());
    }

    public function testRemovesParameter(): void
    {
        $this->subscriber->setParameters([
            'foo' => 'bar',
            'boo' => 'baz',
        ]);
        $this->subscriber->removeParameter('boo');
        $this->assertEquals([
            'foo' => 'bar',
        ], $this->subscriber->getParameters());
    }

    public function testRemovesParameterIfSetToNull(): void
    {
        $this->subscriber->setParameters([
            'foo' => 'bar',
            'boo' => 'baz',
        ]);
        $this->subscriber->setParameter('boo', null);
        $this->assertEquals([
            'foo' => 'bar',
        ], $this->subscriber->getParameters());
    }

    public function testCanSetTopicUrl(): void
    {
        $this->subscriber->setTopicUrl('http://www.example.com/topic');
        $this->assertEquals('http://www.example.com/topic', $this->subscriber->getTopicUrl());
    }

    public function testThrowsExceptionOnSettingEmptyTopicUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->setTopicUrl('');
    }

    public function testThrowsExceptionOnSettingNonStringTopicUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->setTopicUrl(123);
    }

    public function testThrowsExceptionOnSettingInvalidTopicUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->setTopicUrl('http://');
    }

    public function testThrowsExceptionOnMissingTopicUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->getTopicUrl();
    }

    public function testCanSetCallbackUrl(): void
    {
        $this->subscriber->setCallbackUrl('http://www.example.com/callback');
        $this->assertEquals('http://www.example.com/callback', $this->subscriber->getCallbackUrl());
    }

    public function testThrowsExceptionOnSettingEmptyCallbackUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->setCallbackUrl('');
    }

    public function testThrowsExceptionOnSettingNonStringCallbackUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->setCallbackUrl(123);
    }

    public function testThrowsExceptionOnSettingInvalidCallbackUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->setCallbackUrl('http://');
    }

    public function testThrowsExceptionOnMissingCallbackUrl(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->getCallbackUrl();
    }

    public function testCanSetLeaseSeconds(): void
    {
        $this->subscriber->setLeaseSeconds('10000');
        $this->assertEquals(10000, $this->subscriber->getLeaseSeconds());
    }

    public function testThrowsExceptionOnSettingZeroAsLeaseSeconds(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->setLeaseSeconds(0);
    }

    public function testThrowsExceptionOnSettingLessThanZeroAsLeaseSeconds(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->setLeaseSeconds(-1);
    }

    public function testThrowsExceptionOnSettingAnyScalarTypeCastToAZeroOrLessIntegerAsLeaseSeconds(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->setLeaseSeconds('0aa');
    }

    public function testCanSetPreferredVerificationMode(): void
    {
        $this->subscriber->setPreferredVerificationMode(PubSubHubbub::VERIFICATION_MODE_ASYNC);
        $this->assertEquals(PubSubHubbub::VERIFICATION_MODE_ASYNC, $this->subscriber->getPreferredVerificationMode());
    }

    public function testSetsPreferredVerificationModeThrowsExceptionOnSettingBadMode(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->setPreferredVerificationMode('abc');
    }

    public function testPreferredVerificationModeDefaultsToSync(): void
    {
        $this->assertEquals(PubSubHubbub::VERIFICATION_MODE_SYNC, $this->subscriber->getPreferredVerificationMode());
    }

    public function testCanSetStorageImplementation(): void
    {
        $storage = new Subscription($this->tableGateway);
        $this->subscriber->setStorage($storage);
        $this->assertThat($this->subscriber->getStorage(), $this->identicalTo($storage));
    }

    public function testGetStorageThrowsExceptionIfNoneSet(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->subscriber->getStorage();
    }

    // @codingStandardsIgnoreStart
    protected function _getCleanMock(string $className): \PHPUnit\Framework\MockObject\MockObject
    {
        // @codingStandardsIgnoreEnd
        $class       = new ReflectionClass($className);
        $methods     = $class->getMethods();
        $stubMethods = [];
        foreach ($methods as $method) {
            if ($method->isPublic()
                || ($method->isProtected() && $method->isAbstract())
            ) {
                $stubMethods[] = $method->getName();
            }
        }

        $mocked = $this->getMockBuilder($className)
            ->setMethods($stubMethods)
            ->setConstructorArgs([])
            ->setMockClassName(str_replace('\\', '_', $className . '_PubsubSubscriberMock_' . uniqid()))
            ->disableOriginalConstructor()
            ->getMock();
        return $mocked;
    }
}
