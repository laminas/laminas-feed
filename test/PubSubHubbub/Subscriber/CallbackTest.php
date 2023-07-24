<?php

declare(strict_types=1);

namespace LaminasTest\Feed\PubSubHubbub\Subscriber;

use ArrayObject;
use DateInterval;
use DateTime;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Feed\PubSubHubbub\AbstractCallback;
use Laminas\Feed\PubSubHubbub\Exception\ExceptionInterface;
use Laminas\Feed\PubSubHubbub\HttpResponse;
use Laminas\Feed\PubSubHubbub\Model;
use Laminas\Feed\PubSubHubbub\Subscriber\Callback as CallbackSubscriber;
use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use stdClass;

use function file_get_contents;
use function fopen;
use function fwrite;
use function hash;
use function rewind;
use function sprintf;
use function time;

#[BackupGlobals(true)]
class CallbackTest extends TestCase
{
    /** @var CallbackSubscriber */
    public $callback;
    /** @var Adapter&MockObject */
    public $adapter;
    /** @var TableGateway&MockObject */
    public $tableGateway;
    /** @var ResultSet&MockObject */
    public $rowset;
    /** @var array */
    public $get;

    /** @var DateTime */
    public $now;

    protected function setUp(): void
    {
        $this->callback = new CallbackSubscriber();

        $this->adapter      = $this->createMock(Adapter::class);
        $this->tableGateway = $this->createMock(TableGateway::class);
        $this->rowset       = $this->createMock(ResultSet::class);

        $this->tableGateway->expects($this->any())
            ->method('getAdapter')
            ->will($this->returnValue($this->adapter));
        $storage = new Model\Subscription($this->tableGateway);

        $this->now = new DateTime();
        $storage->setNow(clone $this->now);

        $this->callback->setStorage($storage);

        $this->get = [
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
        $r->setValue($callback, $inputStream);
    }

    public function testCanSetHttpResponseObject(): void
    {
        $this->callback->setHttpResponse(new HttpResponse());
        $this->assertInstanceOf(HttpResponse::class, $this->callback->getHttpResponse());
    }

    public function testCanUsesDefaultHttpResponseObject(): void
    {
        $this->assertInstanceOf(HttpResponse::class, $this->callback->getHttpResponse());
    }

    public function testThrowsExceptionOnInvalidHttpResponseObjectSet(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->callback->setHttpResponse(new stdClass());
    }

    public function testThrowsExceptionIfNonObjectSetAsHttpResponseObject(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->callback->setHttpResponse('');
    }

    public function testCanSetSubscriberCount(): void
    {
        $this->callback->setSubscriberCount('10000');
        $this->assertEquals(10000, $this->callback->getSubscriberCount());
    }

    public function testDefaultSubscriberCountIsOne(): void
    {
        $this->assertEquals(1, $this->callback->getSubscriberCount());
    }

    public function testThrowsExceptionOnSettingZeroAsSubscriberCount(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->callback->setSubscriberCount(0);
    }

    public function testThrowsExceptionOnSettingLessThanZeroAsSubscriberCount(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->callback->setSubscriberCount(-1);
    }

    public function testThrowsExceptionOnSettingAnyScalarTypeCastToAZeroOrLessIntegerAsSubscriberCount(): void
    {
        $this->expectException(ExceptionInterface::class);
        $this->callback->setSubscriberCount('0aa');
    }

    public function testCanSetStorageImplementation(): void
    {
        $storage = new Model\Subscription($this->tableGateway);
        $this->callback->setStorage($storage);
        $this->assertThat($this->callback->getStorage(), $this->identicalTo($storage));
    }

    /**
     * @group Laminas_CONFLICT
     */
    public function testValidatesValidHttpGetData(): void
    {
        $mockReturnValue = new class {
            public function getArrayCopy(): array
            {
                return ['verify_token' => hash('sha256', 'cba')];
            }
        };

        $this->tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->rowset));
        $this->rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($mockReturnValue));
        $this->rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->assertTrue($this->callback->isValidHubVerification($this->get));
    }

    public function testReturnsFalseIfHubVerificationNotAGetRequest(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse($this->callback->isValidHubVerification($this->get));
    }

    public function testReturnsFalseIfModeMissingFromHttpGetData(): void
    {
        unset($this->get['hub_mode']);
        $this->assertFalse($this->callback->isValidHubVerification($this->get));
    }

    public function testReturnsFalseIfTopicMissingFromHttpGetData(): void
    {
        unset($this->get['hub_topic']);
        $this->assertFalse($this->callback->isValidHubVerification($this->get));
    }

    public function testReturnsFalseIfChallengeMissingFromHttpGetData(): void
    {
        unset($this->get['hub_challenge']);
        $this->assertFalse($this->callback->isValidHubVerification($this->get));
    }

    public function testReturnsFalseIfVerifyTokenMissingFromHttpGetData(): void
    {
        unset($this->get['hub_verify_token']);
        $this->assertFalse($this->callback->isValidHubVerification($this->get));
    }

    public function testReturnsTrueIfModeSetAsUnsubscribeFromHttpGetData(): void
    {
        $mockReturnValue = new class {
            public function getArrayCopy(): array
            {
                return ['verify_token' => hash('sha256', 'cba')];
            }
        };

        $this->get['hub_mode'] = 'unsubscribe';
        $this->tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->rowset));
        $this->rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($mockReturnValue));
        // require for the count call on the rowset in Model/Subscription
        $this->rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->assertTrue($this->callback->isValidHubVerification($this->get));
    }

    public function testReturnsFalseIfModeNotRecognisedFromHttpGetData(): void
    {
        $this->get['hub_mode'] = 'abc';
        $this->assertFalse($this->callback->isValidHubVerification($this->get));
    }

    public function testReturnsFalseIfLeaseSecondsMissedWhenModeIsSubscribeFromHttpGetData(): void
    {
        unset($this->get['hub_lease_seconds']);
        $this->assertFalse($this->callback->isValidHubVerification($this->get));
    }

    public function testReturnsFalseIfHubTopicInvalidFromHttpGetData(): void
    {
        $this->get['hub_topic'] = 'http://';
        $this->assertFalse($this->callback->isValidHubVerification($this->get));
    }

    public function testReturnsFalseIfVerifyTokenRecordDoesNotExistForConfirmRequest(): void
    {
        $resultSet = $this->createMock(ResultSetInterface::class);
        $resultSet
            ->expects($this->once())
            ->method('count')
            ->willReturn(0);

        $this->tableGateway
            ->expects($this->once())
            ->method('select')
            ->with(['id' => 'verifytokenkey'])
            ->willReturn($resultSet);

        $this->assertFalse($this->callback->isValidHubVerification($this->get));
    }

    /** @todo Determine what this is supposed to actually test */
    public function testReturnsFalseIfVerifyTokenRecordDoesNotAgreeWithConfirmRequest(): void
    {
        $this->markTestSkipped(sprintf(
            '%s needs to be rewritten, as it has the same setup and expectations of'
            . ' testReturnsFalseIfVerifyTokenRecordDoesNotExistForConfirmRequest(),'
            . ' but is clearly meant to test different functionality; unfortunately,'
            . ' not sure what that is currently',
            __METHOD__
        ));
        $this->assertFalse($this->callback->isValidHubVerification($this->get));
    }

    public function testRespondsToInvalidConfirmationWith404Response(): void
    {
        unset($this->get['hub_mode']);
        $this->callback->handle($this->get);
        $this->assertEquals(404, $this->callback->getHttpResponse()->getStatusCode());
    }

    public function testRespondsToValidConfirmationWith200Response(): void
    {
        $this->get['hub_mode'] = 'unsubscribe';
        $this->tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->rowset));

        $t       = clone $this->now;
        $rowdata = [
            'id'            => 'verifytokenkey',
            'verify_token'  => hash('sha256', 'cba'),
            'created_time'  => $t->getTimestamp(),
            'lease_seconds' => 10000,
        ];

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);

        $this->rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->tableGateway->expects($this->once())
            ->method('delete')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue(true));

        $this->callback->handle($this->get);
        $this->assertEquals(200, $this->callback->getHttpResponse()->getStatusCode());
    }

    public function testRespondsToValidConfirmationWithBodyContainingHubChallenge(): void
    {
        $this->tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->rowset));

        $t       = clone $this->now;
        $rowdata = [
            'id'            => 'verifytokenkey',
            'verify_token'  => hash('sha256', 'cba'),
            'created_time'  => $t->getTimestamp(),
            'lease_seconds' => 10000,
        ];

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);

        $this->rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->tableGateway->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo([
                    'id'                 => 'verifytokenkey',
                    'verify_token'       => hash('sha256', 'cba'),
                    'created_time'       => $t->getTimestamp(),
                    'lease_seconds'      => 1_234_567,
                    'subscription_state' => 'verified',
                    'expiration_time'    => $t->add(new DateInterval('PT1234567S'))->format('Y-m-d H:i:s'),
                ]),
                $this->equalTo(['id' => 'verifytokenkey'])
            );

        $this->callback->handle($this->get);
        $this->assertEquals('abc', $this->callback->getHttpResponse()->getContent());
    }

    public function testRespondsToValidFeedUpdateRequestWith200Response(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI']    = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']   = 'application/atom+xml';
        $feedXml                   = file_get_contents(__DIR__ . '/_files/atom10.xml');

        $this->mockInputStream($this->callback, $feedXml);

        $this->tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->rowset));

        $rowdata = [
            'id'           => 'verifytokenkey',
            'verify_token' => hash('sha256', 'cba'),
            'created_time' => time(),
        ];

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);

        $this->rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->callback->handle([]);
        $this->assertEquals(200, $this->callback->getHttpResponse()->getStatusCode());
    }

    public function testRespondsToInvalidFeedUpdateNotPostWith404Response(): void
    {
        // yes, this example makes no sense for GET - I know!!!
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']   = 'application/atom+xml';
        $feedXml                   = file_get_contents(__DIR__ . '/_files/atom10.xml');

        $this->mockInputStream($this->callback, $feedXml);

        $this->callback->handle([]);
        $this->assertEquals(404, $this->callback->getHttpResponse()->getStatusCode());
    }

    public function testRespondsToInvalidFeedUpdateWrongMimeWith404Response(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI']    = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']   = 'application/kml+xml';
        $feedXml                   = file_get_contents(__DIR__ . '/_files/atom10.xml');

        $this->mockInputStream($this->callback, $feedXml);

        $resultSet = $this->createMock(ResultSetInterface::class);
        $resultSet
            ->expects($this->once())
            ->method('count')
            ->willReturn(1);

        $this->tableGateway
            ->expects($this->once())
            ->method('select')
            ->with(['id' => 'verifytokenkey'])
            ->willReturn($resultSet);

        $this->callback->handle([]);
        $this->assertEquals(404, $this->callback->getHttpResponse()->getStatusCode());
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

        $this->mockInputStream($this->callback, $feedXml);

        $this->tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->rowset));

        $rowdata = [
            'id'            => 'verifytokenkey',
            'verify_token'  => hash('sha256', 'cba'),
            'created_time'  => time(),
            'lease_seconds' => 10000,
        ];

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);

        $this->rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->callback->handle([]);
        $this->assertEquals(200, $this->callback->getHttpResponse()->getStatusCode());
    }

    public function testRespondsToValidFeedUpdateWithXHubOnBehalfOfHeader(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI']    = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']   = 'application/atom+xml';
        $feedXml                   = file_get_contents(__DIR__ . '/_files/atom10.xml');

        $this->mockInputStream($this->callback, $feedXml);

        $this->tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(['id' => 'verifytokenkey']))
            ->will($this->returnValue($this->rowset));

        $rowdata = [
            'id'            => 'verifytokenkey',
            'verify_token'  => hash('sha256', 'cba'),
            'created_time'  => time(),
            'lease_seconds' => 10000,
        ];

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);

        $this->rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->callback->handle([]);
        $this->assertEquals(1, $this->callback->getHttpResponse()->getHeader('X-Hub-On-Behalf-Of'));
    }
}
