<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Writer;

use DateTime;
use Laminas\Feed\Writer\Entry;
use Laminas\Feed\Writer\Exception\InvalidArgumentException;
use Laminas\Feed\Writer\Feed;
use Laminas\Feed\Writer\FeedFactory;
use PHPUnit\Framework\TestCase;

class FeedFactoryTest extends TestCase
{
    public function testFactoryShouldCreateFeedWithoutData(): void
    {
        $this->assertInstanceOf(Feed::class, FeedFactory::factory([]));
    }

    public function testFactoryShouldThrowExceptionOnNonTraversableData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        FeedFactory::factory('string');
    }

    public function testFactoryShouldCreateFeedWithBasicData(): void
    {
        // Create
        $data = [
            'feed_link'    => [
                'link' => 'http://www.example.com',
                'type' => 'rss',
            ],
            'date_created' => DateTime::createFromFormat('Y-m-d', '2019-01-15'),
            'copyright'    => 'Copyright (c) 2019',
        ];
        $feed = FeedFactory::factory($data);

        // Test
        $this->assertInstanceOf(Feed::class, $feed);
        $this->assertSame(
            ['rss' => 'http://www.example.com'],
            $feed->getFeedLinks()
        );
        $this->assertSame($data['copyright'], $feed->getCopyright());
        $this->assertInstanceOf(DateTime::class, $feed->getDateCreated());
        $this->assertSame(
            '2019-01-15',
            $feed->getDateCreated()->format('Y-m-d')
        );
    }

    public function testFactoryShouldCreateFeedWithEntryObjects(): void
    {
        $data = [
            'entries' => [
                new Entry(),
                new Entry(),
            ],
        ];

        $feed = FeedFactory::factory($data);
        $this->assertInstanceOf(Feed::class, $feed);
        $this->assertCount(2, $feed);
    }

    public function testFactoryShouldCreateFeedWithEntryArrays(): void
    {
        $data = [
            'entries' => [
                [
                    'date_created' => DateTime::createFromFormat(
                        'Y-m-d',
                        '2019-01-15'
                    ),
                ],
                [
                    'date_created' => DateTime::createFromFormat(
                        'Y-m-d',
                        '2019-01-15'
                    ),
                ],
            ],
        ];

        $feed = FeedFactory::factory($data);
        $this->assertInstanceOf(Feed::class, $feed);
        $this->assertCount(2, $feed);
    }

    public function testFactoryShouldThrowExceptionOnNonTraversableEntriesData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        FeedFactory::factory(
            [
                'entries' => 'string',
            ]
        );
    }

    public function testFactoryShouldThrowExceptionOnNonTraversableEntryData(): void
    {
        $this->expectException(InvalidArgumentException::class);
        FeedFactory::factory(
            [
                'entries' => [
                    'string',
                ],
            ]
        );
    }
}
