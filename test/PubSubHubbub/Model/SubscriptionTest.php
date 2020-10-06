<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\PubSubHubbub\Model;

use DateTime;
use Laminas\Db\Adapter\Adapter as DbAdapter;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Feed\PubSubHubbub\Model\Subscription;
use Laminas\Feed\PubSubHubbub\Model\SubscriptionPersistenceInterface;
use PDO;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Pubsubhubbub_Model
 */
class SubscriptionTest extends TestCase
{
    /**
     * @group Laminas-10069
     */
    public function testAllOperations(): void
    {
        $adapter = $this->initDb();
        $table   = new TableGateway('subscription', $adapter);

        $subscription = new Subscription($table);

        $id = uniqid();
        $this->assertFalse($subscription->hasSubscription($id));
        $this->assertFalse($subscription->getSubscription($id));
        $this->assertFalse($subscription->deleteSubscription($id));
        $this->assertTrue($subscription->setSubscription(['id' => $id]));

        $this->assertTrue($subscription->hasSubscription($id));
        $dataSubscription = $subscription->getSubscription($id);
        $this->assertIsArray($dataSubscription);
        $keys = [
            'id',
            'topic_url',
            'hub_url',
            'created_time',
            'lease_seconds',
            'verify_token',
            'secret',
            'expiration_time',
            'subscription_state',
        ];

        $this->assertSame($keys, array_keys($dataSubscription));
        $this->assertFalse($subscription->setSubscription(['id' => $id]));
        $this->assertTrue($subscription->deleteSubscription($id));
    }

    public function testImpemetsSubscriptionInterface(): void
    {
        $reflection = new ReflectionClass(Subscription::class);
        $this->assertTrue(
            $reflection->implementsInterface(SubscriptionPersistenceInterface::class)
        );
        unset($reflection);
    }

    public function testCurrentTimeSetterAndGetter(): void
    {
        $now          = new DateTime();
        $subscription = new Subscription(new TableGateway('subscription', $this->initDb()));
        $subscription->setNow($now);
        $this->assertSame($subscription->getNow(), $now);
    }

    protected function initDb()
    {
        if (! extension_loaded('pdo')
            || ! in_array('sqlite', PDO::getAvailableDrivers())
        ) {
            $this->markTestSkipped('Test only with pdo_sqlite');
        }
        $db = new DbAdapter(['driver' => 'pdo_sqlite', 'dsn' => 'sqlite::memory:']);
        $this->createTable($db);

        return $db;
    }

    protected function createTable(DbAdapter $db)
    {
        $sql = 'CREATE TABLE subscription ('
            . "id varchar(32) PRIMARY KEY NOT NULL DEFAULT '', "
            . 'topic_url varchar(255) DEFAULT NULL, '
            . 'hub_url varchar(255) DEFAULT NULL, '
            . 'created_time datetime DEFAULT NULL, '
            . 'lease_seconds bigint(20) DEFAULT NULL, '
            . 'verify_token varchar(255) DEFAULT NULL, '
            . 'secret varchar(255) DEFAULT NULL, '
            . 'expiration_time datetime DEFAULT NULL, '
            . 'subscription_state varchar(12) DEFAULT NULL'
            . ');';

        $db->query($sql)->execute();
    }
}
