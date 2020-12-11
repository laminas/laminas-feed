<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Writer\Extension\PodcastIndex;

use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Exception\ExceptionInterface;
use PHPUnit\Framework\TestCase;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Writer
 */
class FeedTest extends TestCase
{
    public function testSetLocked(): void
    {
        $feed = new Writer\Feed();

        $locked = [
            'value' => 'yes',
            'owner' => 'john.doe@example.com',
        ];
        $feed->setPodcastIndexLocked($locked);
        $this->assertEquals($locked, $feed->getPodcastIndexLocked());
    }

    public function testSetLockedThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $locked = [
            'abc' => 'def',
        ];
        $this->expectException(ExceptionInterface::class);
        $feed->setPodcastIndexLocked($locked);
    }

    public function nonAlphaValues(): array
    {
        return [
            'null' => [null],
            'zero' => [0],
            'int' => [1],
            'zero-float' => [0.0],
            'float' => [1.1],
            'string' => ['1'],
            'array' => [['yes']],
            'object' => [(object) ['value' => 'yes']],
        ];
    }

    /**
     * @dataProvider nonAlphaValues
     *
     * @param mixed $value
     */
    public function testSetLockedThrowsExceptionOnNonAlphaValue($value): void
    {
        $feed = new Writer\Feed();

        $locked = [
            'value' => $value,
            'owner' => 'john.doe@example.com',
        ];
        $this->expectException(ExceptionInterface::class);
        $feed->setPodcastIndexLocked($locked);
    }

    public function testSetFunding(): void
    {
        $feed = new Writer\Feed();

        $funding = [
            'title' => 'Support the show!',
            'url' => 'http://example.com/donate',
        ];
        $feed->setPodcastIndexFunding($funding);
        $this->assertEquals($funding, $feed->getPodcastIndexFunding());
    }

    public function testSetFundingThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $locked = [
            'abc' => 'def',
        ];
        $this->expectException(ExceptionInterface::class);
        $feed->setPodcastIndexFunding($locked);
    }
}
