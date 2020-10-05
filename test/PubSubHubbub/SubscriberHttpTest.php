<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\PubSubHubbub;

use Laminas\Feed\PubSubHubbub\Model\Subscription;
use Laminas\Feed\PubSubHubbub\PubSubHubbub;
use Laminas\Feed\PubSubHubbub\Subscriber;
use Laminas\Http\Client\Adapter\Socket;
use Laminas\Http\Client as HttpClient;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Note that $this->_baseuri must point to a directory on a web server
 * containing all the files under the files directory. You should symlink
 * or copy these files and set '_baseuri' properly using the constant in
 * phpunit.xml (based on phpunit.xml.dist)
 *
 * You can also set the proper constant in your test configuration file to
 * point to the right place.
 *
 * @group Laminas_Feed
 * @group Laminas_Feed_Subsubhubbub
 */
class SubscriberHttpTest extends TestCase
{
    /** @var Subscriber */
    protected $subscriber;

    /** @var string */
    protected $baseuri;

    /** @var HttpClient */
    protected $client;

    protected $storage;

    protected function setUp(): void
    {
        $this->baseuri = getenv('TESTS_LAMINAS_FEED_PUBSUBHUBBUB_BASEURI');
        if ($this->baseuri) {
            if (substr($this->baseuri, -1) !== '/') {
                $this->baseuri .= '/';
            }
            $name = $this->getName();
            if (($pos = strpos($name, ' ')) !== false) {
                $name = substr($name, 0, $pos);
            }
            $uri          = $this->baseuri . $name . '.php';
            $this->client = new HttpClient($uri);
            $this->client->setAdapter(Socket::class);
            PubSubHubbub::setHttpClient($this->client);
            $this->subscriber = new Subscriber();

            $this->storage = $this->_getCleanMock(Subscription::class);
            $this->subscriber->setStorage($this->storage);
        } else {
            // Skip tests
            $this->markTestSkipped('Laminas\Feed\PubSubHubbub\Subscriber dynamic tests are not enabled in phpunit.xml');
        }
    }

    public function testSubscriptionRequestSendsExpectedPostData(): void
    {
        $this->subscriber->setTopicUrl('http://www.example.com/topic');
        $this->subscriber->addHubUrl($this->baseuri . '/testRawPostData.php');
        $this->subscriber->setCallbackUrl('http://www.example.com/callback');
        $this->subscriber->setTestStaticToken('abc'); // override for testing
        $this->subscriber->subscribeAll();
        $this->assertEquals(
            'hub.callback=http%3A%2F%2Fwww.example.com%2Fcallback%3Fxhub.subscription%3D5536df06b5d'
            . 'cb966edab3a4c4d56213c16a8184b&hub.lease_seconds=2592000&hub.mode='
            . 'subscribe&hub.topic=http%3A%2F%2Fwww.example.com%2Ftopic&hub.veri'
            . 'fy=sync&hub.verify=async&hub.verify_token=abc',
            $this->client->getResponse()->getBody()
        );
    }

    public function testUnsubscriptionRequestSendsExpectedPostData(): void
    {
        $this->subscriber->setTopicUrl('http://www.example.com/topic');
        $this->subscriber->addHubUrl($this->baseuri . '/testRawPostData.php');
        $this->subscriber->setCallbackUrl('http://www.example.com/callback');
        $this->subscriber->setTestStaticToken('abc'); //override for testing
        $this->subscriber->unsubscribeAll();
        $this->assertEquals(
            'hub.callback=http%3A%2F%2Fwww.example.com%2Fcallback%3Fxhub.subscription%3D5536df06b5d'
            . 'cb966edab3a4c4d56213c16a8184b&hub.mode=unsubscribe&hub.topic=http'
            . '%3A%2F%2Fwww.example.com%2Ftopic&hub.verify=sync&hub.verify=async'
            . '&hub.verify_token=abc',
            $this->client->getResponse()->getBody()
        );

        $subscriptionRecord = $this->subscriber->getStorage()->getSubscription();
        $this->assertEquals($subscriptionRecord['subscription_state'], PubSubHubbub::SUBSCRIPTION_TODELETE);
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
        $mocked = $this->getMockBuilder($className)->setMethods($stubMethods)->getMock();
        return $mocked;
    }
}
