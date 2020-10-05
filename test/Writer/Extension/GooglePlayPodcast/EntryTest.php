<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Writer\Extension\GooglePlayPodcast;

use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Exception\ExceptionInterface;
use PHPUnit\Framework\TestCase;

class EntryTest extends TestCase
{
    public function testSetBlock(): void
    {
        $entry = new Writer\Entry();
        $entry->setPlayPodcastBlock('yes');
        $this->assertEquals('yes', $entry->getPlayPodcastBlock());
    }

    public function testSetBlockThrowsExceptionOnNonAlphaValue(): void
    {
        $entry = new Writer\Entry();

        $this->expectException(ExceptionInterface::class);
        $entry->setPlayPodcastBlock('123');
    }

    public function testSetBlockThrowsExceptionIfValueGreaterThan255CharsLength(): void
    {
        $entry = new Writer\Entry();

        $this->expectException(ExceptionInterface::class);
        $entry->setPlayPodcastBlock(str_repeat('a', 256));
    }

    public function testSetExplicitToYes(): void
    {
        $entry = new Writer\Entry();
        $entry->setPlayPodcastExplicit('yes');
        $this->assertEquals('yes', $entry->getPlayPodcastExplicit());
    }

    public function testSetExplicitToNo(): void
    {
        $entry = new Writer\Entry();
        $entry->setPlayPodcastExplicit('no');
        $this->assertEquals('no', $entry->getPlayPodcastExplicit());
    }

    public function testSetExplicitToClean(): void
    {
        $entry = new Writer\Entry();
        $entry->setPlayPodcastExplicit('clean');
        $this->assertEquals('clean', $entry->getPlayPodcastExplicit());
    }

    public function testSetExplicitThrowsExceptionOnUnknownTerm(): void
    {
        $entry = new Writer\Entry();

        $this->expectException(ExceptionInterface::class);
        $entry->setPlayPodcastExplicit('abc');
    }

    public function testSetDescription(): void
    {
        $entry = new Writer\Entry();
        $entry->setPlayPodcastDescription('abc');
        $this->assertEquals('abc', $entry->getPlayPodcastDescription());
    }

    public function testSetDescriptionThrowsExceptionWhenValueExceeds255Chars(): void
    {
        $entry = new Writer\Entry();

        $this->expectException(ExceptionInterface::class);
        $entry->setPlayPodcastDescription(str_repeat('a', 4001));
    }
}
