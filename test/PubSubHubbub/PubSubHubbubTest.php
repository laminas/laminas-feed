<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\PubSubHubbub;

use Laminas\Feed\PubSubHubbub\PubSubHubbub;
use Laminas\Feed\Reader\Reader as FeedReader;

/**
 * @category   Laminas
 * @package    Laminas_Feed
 * @subpackage UnitTests
 * @group      Laminas_Feed
 * @group      Laminas_Feed_Subsubhubbub
 */
class PubSubHubbubTest extends \PHPUnit_Framework_TestCase
{
    public function testCanDetectHubs()
    {
        $feed = FeedReader::importFile(__DIR__ . '/_files/rss20.xml');
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), PubSubHubbub::detectHubs($feed));
    }
}
