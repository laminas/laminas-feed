<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Reader;

use Laminas\Feed\Reader\FeedSet;
use PHPUnit_Framework_TestCase;
use ReflectionMethod;

class FeedSetTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FeedSet
     */
    protected $feedSet;

    protected function setUp()
    {
        $this->feedSet = new FeedSet();
    }

    /**
     * @dataProvider linkAndUriProvider
     */
    public function testAbsolutiseUri($link, $uri)
    {
        $method = new ReflectionMethod('Laminas\Feed\Reader\FeedSet', 'absolutiseUri');
        $method->setAccessible(true);

        $this->assertEquals('http://example.com/feed', $method->invoke($this->feedSet, $link, $uri));
    }

    public function linkAndUriProvider()
    {
        return [
            'fully-qualified'   => ['feed', 'http://example.com'],
            'scheme-relative'   => ['feed', '//example.com'],
            'double-slash-path' => ['//feed','//example.com'],
        ];
    }
}
