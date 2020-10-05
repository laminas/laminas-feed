<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\PubSubHubbub;

use Laminas\Feed\PubSubHubbub\PubSubHubbub;
use Laminas\Feed\Reader\Reader as FeedReader;
use PHPUnit\Framework\TestCase;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Subsubhubbub
 */
class PubSubHubbubTest extends TestCase
{
    public function testCanDetectHubs(): void
    {
        $feed = FeedReader::importFile(__DIR__ . '/_files/rss20.xml');
        $this->assertEquals([
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
        ], PubSubHubbub::detectHubs($feed));
    }
}
