<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Reader;

use Laminas\Feed\Reader\FeedSet;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class FeedSetTest extends TestCase
{
    /**
     * @var FeedSet
     */
    protected $feedSet;

    protected function setUp(): void
    {
        $this->feedSet = new FeedSet();
    }

    /**
     * @param string $link
     * @param string|null $uri
     * @param string|null $result
     * @dataProvider linkAndUriProvider
     */
    public function testAbsolutiseUri($link, $uri, $result): void
    {
        $method = new ReflectionMethod(FeedSet::class, 'absolutiseUri');
        $method->setAccessible(true);

        $this->assertEquals($result, $method->invoke($this->feedSet, $link, $uri));
    }

    /**
     * @psalm-return array<string,array{0:string,1:string|null,2:string|null}>
     */
    public function linkAndUriProvider(): array
    {
        return [
            'fully-qualified'         => ['feed', 'http://example.com', 'http://example.com/feed'],
            'default-scheme'          => ['feed', '//example.com', 'http://example.com/feed'],
            'relative-path'           => ['./feed', 'http://example.com/page', 'http://example.com/page/feed'],
            'relative-path-parent'    => ['../feed', 'http://example.com/page', 'http://example.com/feed'],
            'scheme-relative'         => ['//example.com/feed', 'https://example.org', 'https://example.com/feed'],
            'scheme-relative-default' => ['//example.com/feed', '//example.org', 'http://example.com/feed'],
            'invalid-absolute'        => ['ftp://feed', 'http://example.com', null],
            'invalid'                 => ['', null, null],
        ];
    }
}
