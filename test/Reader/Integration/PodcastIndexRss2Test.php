<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Reader\Integration;

use Laminas\Feed\Reader;
use PHPUnit\Framework\TestCase;
use stdClass;

use function file_get_contents;
use function implode;

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
        $this->feedSamplePath = __DIR__ . '/_files/podcastindex.xml';
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
        $this->assertEquals('john.doe@example.com', $feed->getPodcastIndexLockOwner());
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
        $this->assertEquals($expected, $feed->getPodcastIndexFunding());
    }

    public function testGetsLicense(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expected             = new stdClass();
        $expected->identifier = 'my-podcast-license-v1';
        $expected->url        = 'https://example.org/mypodcastlicense/full.pdf';

        $this->assertEquals($expected, $feed->getPodcastIndexLicense());
    }

    public function testGetsLocation(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expected              = new stdClass();
        $expected->description = 'Austin';
        $expected->geo         = 'geo:30.2711286,-97.7436995';
        $expected->osm         = 'R113314';

        $this->assertEquals($expected, $feed->getPodcastIndexLocation());
    }

    public function testGetsImages(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $srcset = [
            "https://example.com/images/ep1/pci_avatar-massive.jpg 1500w",
            "https://example.com/images/ep1/pci_avatar-middle.jpg 600w",
            "https://example.com/images/ep1/pci_avatar-small.jpg 300w",
            "https://example.com/images/ep1/pci_avatar-tiny.jpg 150w",
        ];

        $expected         = new stdClass();
        $expected->srcset = implode(', ', $srcset);

        $this->assertEquals($expected, $feed->getPodcastIndexImages());
    }

    public function testGetsUpdateFrequency(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expected              = new stdClass();
        $expected->description = 'Every other Monday';
        $expected->complete    = 'false';
        $expected->dtstart     = '2023-08-28T00:00:00.000Z';
        $expected->rrule       = 'FREQ=WEEKLY';

        $this->assertEquals($expected, $feed->getPodcastIndexUpdateFrequency());
    }

    public function testGetsPeople(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expected        = new stdClass();
        $expected->name  = 'Alice Brown';
        $expected->role  = 'guest';
        $expected->group = 'writing';
        $expected->img   = 'http://example.com/images/alicebrown.jpg';
        $expected->href  = 'https://www.wikipedia/alicebrown';

        $people = $feed->getPodcastIndexPeople();
        $this->assertEquals($expected, $people[0]);
    }

    /**
     * Entry level testing
     */
    public function testGetsEntryTranscript(): void
    {
        $feed = Reader\Reader::importString(
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
        $feed = Reader\Reader::importString(
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
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $expected            = new stdClass();
        $expected->title     = 'Pepper shakers comparison';
        $expected->startTime = '66.0';
        $expected->duration  = '39.0';

        $this->assertEquals([
            $expected,
        ], $entry->getSoundbites());
    }
}
