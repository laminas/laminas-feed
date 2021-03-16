<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Reader\Integration;

use Laminas\Feed\Reader;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Reader
 */
class PodcastIndexRss2Test extends TestCase
{
    /** @var string */
    protected $feedSamplePath;

    protected function setUp(): void
    {
        Reader\Reader::reset();
        $this->feedSamplePath = dirname(__FILE__) . '/_files/podcastindex.xml';
    }

    /**
     * Feed level testing
     */
    public function testGetsLocked(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(true, $feed->isLocked());
    }

    public function testGetsLockOwner(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('john.doe@example.com', $feed->getLockOwner());
    }


    public function testGetsFunding(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expected        = new stdClass();
        $expected->url   = 'http://example.com/donate';
        $expected->title = 'Support the show!';

        $this->assertEquals($expected, $feed->getFunding());
    }

    /**
     * Entry level testing
     *
     */
    public function testGetsEntryTranscript(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $expected           = new stdClass();
        $expected->url      = 'https://example.com/podcasts/everything/TranscriptEpisode3.html';
        $expected->type     = 'text/html';
        $expected->language = '';
        $expected->rel      = '';

        $this->assertEquals($expected, $entry->getTranscript());
    }

    public function testGetsEntryChapters(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $expected       = new stdClass();
        $expected->url  = 'https://example.com/podcasts/everything/ChaptersEpisode3.json';
        $expected->type = 'application/json+chapters';

        $this->assertEquals($expected, $entry->getChapters());
    }

    public function testGetsEntrySoundbites(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $expected            = new stdClass();
        $expected->title     = 'Pepper shakers comparison';
        $expected->startTime = '66.0';
        $expected->duration  = '39.0';

        $this->assertEquals([
            $expected
        ], $entry->getSoundbites());
    }
}
