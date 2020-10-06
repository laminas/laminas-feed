<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\PubSubHubbub\Subscriber;

use ArrayObject;
use DateInterval;
use DateTime;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Feed\PubSubHubbub\AbstractCallback;
use Laminas\Feed\PubSubHubbub\Exception\ExceptionInterface;
use Laminas\Feed\PubSubHubbub\HttpResponse;
use Laminas\Feed\PubSubHubbub\Model;
use Laminas\Feed\PubSubHubbub\Subscriber\Callback as CallbackSubscriber;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use stdClass;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Subsubhubbub
 */
class CallbackTest extends TestCase
{
    // @codingStandardsIgnoreStart
    /** @var CallbackSubscriber */
    public $_callback;
    /** @var \Laminas\Db\Adapter\Adapter|\PHPUnit_Framework_MockObject_MockObject */
    public $_adapter;
    /** @var \Laminas\Db\TableGateway\TableGateway|\PHPUnit_Framework_MockObject_MockObject */
    public $_tableGateway;
    /** @var \Laminas\Db\ResultSet\ResultSet|\PHPUnit_Framework_MockObject_MockObject */
    public $_rowset;
    /** @var array */
    public $_get;
    // @codingStandardsIgnoreEnd

    /** @var DateTime */
    public $now;

    protected function setUp(): void
    {
        $this->_callback = new CallbackSubscriber();

        $this->_adapter      = $this->_getCleanMock(
            Adapter::class
        );
        $this->_tableGateway = $this->_getCleanMock(
            TableGateway::class
        );
        $this->_rowset       = $this->_getCleanMock(
            ResultSet::class
        );

        $this->_tableGateway->expects($this->any())
            ->method('getAdapter')
            ->will($this->returnValue($this->_adapter));
        $storage = new Model\Subscription($this->_tableGateway);

        $this->now = new DateTime();
        $storage->setNow(clone $this->now);

        $this->_callback->setStorage($storage);

        $this->_get = [
            'hub_mode'          => 'subscribe',
            'hub_topic'         => 'http://www.example.com/topic',
            'hub_challenge'     => 'abc',
            'hub_verify_token'  => 'cba',
            'hub_lease_seconds' => '1234567',
        ];

        $_SERVER['REQUEST_METHOD'] = 'get';
        $_SERVER['QUERY_STRING']   = 'xhub.subscription=verifytokenkey';
    }

    /**
     * Mock the input stream that the callback will read from.
     *
     * Creates a php://temp stream based on $contents, that is then injected as
     * the $inputStream property of the callback via reflection.
     *
     * @param string $contents
     */
    public function mockInputStream(AbstractCallback $callback, $contents)
    {
        $inputStream = fopen('php://temp', 'wb+');
        fwrite($inputStream, $contents);
        rewind($inputStream);

        $r = new ReflectionProperty($callback, 'inputStream');
        $r->setAccessible(true);
        $r->setValue($callback, $inputStream);
    }

    public function testCanSetHttpResponseObject(): void
    {
        $this->_callback->setHttpResponse(new HttpResponse());
        $this->assertInstanceOf(HttpResponse::class, $this->_callback->getHttpResponse());
    }

    public function testCanUsesDefaultHttpResponseObject(): void
    {
        $this->assertInstanceOf(HttpResponse::class, $this->_callback->getHttpResponse());
    }

    public function testThrowsExceptionOnInvalidHttpResponseObjectSet(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->_callback->setHttpResponse(new stdClass());
    }

    public function testThrowsExceptionIfNonObjectSetAsHttpResponseObject(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->_callback->setHttpResponse('');
    }

    public function testCanSetSubscriberCount(): void
    {
        $this->_callback->setSubscriberCount('10000');
        $this->assertEquals(10000, $this->_callback->getSubscriberCount());
    }

    public function testDefaultSubscriberCountIsOne(): void
    {
        $this->assertEquals(1, $this->_callback->getSubscriberCount());
    }

    public function testThrowsExceptionOnSettingZeroAsSubscriberCount(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->_callback->setSubscriberCount(0);
    }

    public function testThrowsExceptionOnSettingLessThanZeroAsSubscriberCount(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->_callback->setSubscriberCount(-1);
    }

    public function testThrowsExceptionOnSettingAnyScalarTypeCastToAZeroOrLessIntegerAsSubscriberCount(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->_callback->setSubscriberCount('0aa');
    }

    public function testCanSetStorageImplementation(): void
    {
        $storage = new Model\Subscription($this->_tableGateway);
        $this->_callback->setStorage($storage);
        $this->assertThat($this->_callback->getStorage(), $this->identicalTo($storage));
    }

    /**
     * @group Laminas_CONFLICT
     */
    public function testValidatesValidHttpGetData(): void
    {
        $mockReturnValue = $this->getMockBuilder('Result')->setMethods(['getArrayCopy'])->getMock();
        $mockReturnValue->expects($this->any())
            ->method('getArrayCopy')
            ->will($this->returnValue([
                'verify_token' => hash('sha256', 'cba'),
            ]));

        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->_rowset));
        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($mockReturnValue));
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->assertTrue($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfHubVerificationNotAGetRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfModeMissingFromHttpGetData(): void
    {
        unset($this->_get['hub_mode']);
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfTopicMissingFromHttpGetData(): void
    {
        unset($this->_get['hub_topic']);
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfChallengeMissingFromHttpGetData(): void
    {
        unset($this->_get['hub_challenge']);
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfVerifyTokenMissingFromHttpGetData(): void
    {
        unset($this->_get['hub_verify_token']);
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsTrueIfModeSetAsUnsubscribeFromHttpGetData(): void
    {
        $mockReturnValue = $this->getMockBuilder('Result')->setMethods(['getArrayCopy'])->getMock();
        $mockReturnValue->expects($this->any())
            ->method('getArrayCopy')
            ->will($this->returnValue([
                'verify_token' => hash('sha256', 'cba'),
            ]));

        $this->_get['hub_mode'] = 'unsubscribe';
        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->_rowset));
        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($mockReturnValue));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->assertTrue($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfModeNotRecognisedFromHttpGetData(): void
    {
        $this->_get['hub_mode'] = 'abc';
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfLeaseSecondsMissedWhenModeIsSubscribeFromHttpGetData(): void
    {
        unset($this->_get['hub_lease_seconds']);
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfHubTopicInvalidFromHttpGetData(): void
    {
        $this->_get['hub_topic'] = 'http://';
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfVerifyTokenRecordDoesNotExistForConfirmRequest(): void
    {
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfVerifyTokenRecordDoesNotAgreeWithConfirmRequest(): void
    {
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testRespondsToInvalidConfirmationWith404Response(): void
    {
        unset($this->_get['hub_mode']);
        $this->_callback->handle($this->_get);
        $this->assertEquals(404, $this->_callback->getHttpResponse()->getStatusCode());
    }

    public function testRespondsToValidConfirmationWith200Response(): void
    {
        $this->_get['hub_mode'] = 'unsubscribe';
        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->_rowset));

        $t       = clone $this->now;
        $rowdata = [
            'id'            => 'verifytokenkey',
            'verify_token'  => hash('sha256', 'cba'),
            'created_time'  => $t->getTimestamp(),
            'lease_seconds' => 10000,
        ];

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);

        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->_tableGateway->expects($this->once())
            ->method('delete')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue(true));

        $this->_callback->handle($this->_get);
        $this->assertEquals(200, $this->_callback->getHttpResponse()->getStatusCode());
    }

    public function testRespondsToValidConfirmationWithBodyContainingHubChallenge(): void
    {
        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->_rowset));

        $t       = clone $this->now;
        $rowdata = [
            'id'            => 'verifytokenkey',
            'verify_token'  => hash('sha256', 'cba'),
            'created_time'  => $t->getTimestamp(),
            'lease_seconds' => 10000,
        ];

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);

        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->_tableGateway->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo([
                    'id'                 => 'verifytokenkey',
                    'verify_token'       => hash('sha256', 'cba'),
                    'created_time'       => $t->getTimestamp(),
                    'lease_seconds'      => 1234567,
                    'subscription_state' => 'verified',
                    'expiration_time'    => $t->add(new DateInterval('PT1234567S'))->format('Y-m-d H:i:s'),
                ]),
                $this->equalTo(['id' => 'verifytokenkey'])
            );

        $this->_callback->handle($this->_get);
        $this->assertEquals('abc', $this->_callback->getHttpResponse()->getContent());
    }

    public function testRespondsToValidFeedUpdateRequestWith200Response(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI']    = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']   = 'application/atom+xml';
        $feedXml                   = file_get_contents(__DIR__ . '/_files/atom10.xml');

        $this->mockInputStream($this->_callback, $feedXml);

        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->_rowset));

        $rowdata = [
            'id'           => 'verifytokenkey',
            'verify_token' => hash('sha256', 'cba'),
            'created_time' => time(),
        ];

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);

        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->_callback->handle([]);
        $this->assertEquals(200, $this->_callback->getHttpResponse()->getStatusCode());
    }

    public function testRespondsToInvalidFeedUpdateNotPostWith404Response(): void
    {
        // yes, this example makes no sense for GET - I know!!!
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']   = 'application/atom+xml';
        $feedXml                   = file_get_contents(__DIR__ . '/_files/atom10.xml');

        $this->mockInputStream($this->_callback, $feedXml);

        $this->_callback->handle([]);
        $this->assertEquals(404, $this->_callback->getHttpResponse()->getStatusCode());
    }

    public function testRespondsToInvalidFeedUpdateWrongMimeWith404Response(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI']    = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']   = 'application/kml+xml';
        $feedXml                   = file_get_contents(__DIR__ . '/_files/atom10.xml');

        $this->mockInputStream($this->_callback, $feedXml);

        $this->_callback->handle([]);
        $this->assertEquals(404, $this->_callback->getHttpResponse()->getStatusCode());
    }

    /**
     * As a judgement call, we must respond to any successful request, regardless
     * of the wellformedness of any XML payload, by returning a 2xx response code.
     * The validation of feeds and their processing must occur outside the Hubbub
     * protocol.
     */
    public function testRespondsToInvalidFeedUpdateWrongFeedTypeForMimeWith200Response(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI']    = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']   = 'application/rss+xml';
        $feedXml                   = file_get_contents(__DIR__ . '/_files/atom10.xml');

        $this->mockInputStream($this->_callback, $feedXml);

        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->_rowset));

        $rowdata = [
            'id'            => 'verifytokenkey',
            'verify_token'  => hash('sha256', 'cba'),
            'created_time'  => time(),
            'lease_seconds' => 10000,
        ];

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);

        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->_callback->handle([]);
        $this->assertEquals(200, $this->_callback->getHttpResponse()->getStatusCode());
    }

    public function testRespondsToValidFeedUpdateWithXHubOnBehalfOfHeader(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI']    = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']   = 'application/atom+xml';
        $feedXml                   = file_get_contents(__DIR__ . '/_files/atom10.xml');

        $this->mockInputStream($this->_callback, $feedXml);

        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->_rowset));

        $rowdata = [
            'id'            => 'verifytokenkey',
            'verify_token'  => hash('sha256', 'cba'),
            'created_time'  => time(),
            'lease_seconds' => 10000,
        ];

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);

        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->_callback->handle([]);
        $this->assertEquals(1, $this->_callback->getHttpResponse()->getHeader('X-Hub-On-Behalf-Of'));
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
