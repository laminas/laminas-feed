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
        $this->assertEquals(true, $feed->isPodcastIndexLocked());
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
        $expected->rel         = 'subject';
        $expected->country     = 'US';

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

    public function testGetsPersons(): void
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

        $people = $feed->getPodcastIndexPersons();
        $this->assertEquals($expected, $people[0]);
    }

    public function testGetsTrailer(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expected          = new stdClass();
        $expected->title   = 'Season 4: Race for the Clouds';
        $expected->pubdate = "Thu, 01 Apr 2021 08:00:00 EST";
        $expected->url     = "https://example.org/season4teaser.mp4";
        $expected->length  = 12345678;
        $expected->type    = "video/mp4";
        $expected->season  = 4;

        $this->assertEquals($expected, $feed->getPodcastIndexTrailer());
    }

    public function testGetsGuid(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expected        = new stdClass();
        $expected->value = '917393e3-1b1e-5cef-ace4-edaa54e1f810';

        $this->assertEquals($expected, $feed->getPodcastIndexGuid());
    }

    public function testGetsMedium(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expected        = new stdClass();
        $expected->value = 'audiobook';

        $this->assertEquals($expected, $feed->getPodcastIndexMedium());
    }

    public function testGetsBlocks(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expectedA        = new stdClass();
        $expectedA->value = 'yes';
        $expectedA->id    = '';

        $expectedB        = new stdClass();
        $expectedB->value = 'no';
        $expectedB->id    = 'google';

        $blocks = $feed->getPodcastIndexBlocks();
        $this->assertEquals($expectedA, $blocks[0]);
        $this->assertEquals($expectedB, $blocks[1]);
    }

    public function testGetsTxts(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expectedA          = new stdClass();
        $expectedA->value   = 'S6lpp-7ZCn8-dZfGc-OoyaG';
        $expectedA->purpose = 'verify';

        $expectedB          = new stdClass();
        $expectedB->value   = '2022-10-26T04:45:30.742Z';
        $expectedB->purpose = 'release';

        $txts = $feed->getPodcastIndexTxts();
        $this->assertEquals($expectedA, $txts[0]);
        $this->assertEquals($expectedB, $txts[1]);
    }

    public function testGetsPodping(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expected              = new stdClass();
        $expected->usesPodping = true;

        $this->assertEquals($expected, $feed->getPodcastIndexPodping());
    }

    public function testGetsRemoteItems(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expectedA           = new stdClass();
        $expectedA->feedGuid = '29cdca4a-xxxx-yyyy-b48b-09a011c5daa9';
        $expectedA->feedUrl  = '';
        $expectedA->itemGuid = '';
        $expectedA->medium   = '';
        $expectedA->title    = '';

        $expectedB           = new stdClass();
        $expectedB->feedGuid = '917393e3-1b1e-5cef-ace4-edaa54e1f810';
        $expectedB->feedUrl  = 'https://feeds.example.org/917393e3-1b1e-5cef-ace4-edaa54e1f810/rss.xml';
        $expectedB->itemGuid = '';
        $expectedB->medium   = 'podcast';
        $expectedB->title    = 'Some Example';

        $items = $feed->getPodcastIndexRemoteItems();
        $this->assertEquals($expectedA, $items[0]);
        $this->assertEquals($expectedB, $items[1]);
    }

    public function testGetsPodroll(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expectedA           = new stdClass();
        $expectedA->feedGuid = '29cdca4a-32d8-56ba-b48b-09a011c5daa9';
        $expectedA->feedUrl  = '';
        $expectedA->itemGuid = '';
        $expectedA->medium   = '';
        $expectedA->title    = '';

        $expectedB           = new stdClass();
        $expectedB->feedGuid = '917393e3-1b1e-5cef-ace4-edaa54e1f810';
        $expectedB->feedUrl  = 'https://feeds.example.org/917393e3-1b1e-5cef-ace4-edaa54e1f810/rss.xml';
        $expectedB->itemGuid = 'asdf089j0-ep240-20230510';
        $expectedB->medium   = 'music';
        $expectedB->title    = 'Here Comes the Sun';

        $items = $feed->getPodcastIndexPodroll();
        $this->assertEquals($expectedA, $items[0]);
        $this->assertEquals($expectedB, $items[1]);
    }

    public function testGetsPublisher(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expectedA           = new stdClass();
        $expectedA->feedGuid = 'publisher-guid-56ba-b48b-09a011c5daa9';
        $expectedA->feedUrl  = '';
        $expectedA->itemGuid = '';
        $expectedA->medium   = '';
        $expectedA->title    = '';

        $publisherItem = $feed->getPodcastIndexPublisher();
        $this->assertEquals($expectedA, $publisherItem);
    }

    public function testGetsValues(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $recipA              = new stdClass();
        $recipA->name        = "Alice (Podcaster)";
        $recipA->type        = "node";
        $recipA->address     = "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52";
        $recipA->split       = '40';
        $recipA->customKey   = '';
        $recipA->customValue = '';
        $recipA->fee         = '';

        $recipB              = new stdClass();
        $recipB->name        = "Bob (Podcaster)";
        $recipB->type        = "node";
        $recipB->address     = "032f4ffbbafffbe51726ad3c164a3d0d37ec27bc67b29a159b0f49ae8ac21b8508";
        $recipB->split       = '60';
        $recipB->customKey   = '';
        $recipB->customValue = '';
        $recipB->fee         = '';

        $expected             = new stdClass();
        $expected->type       = 'lightning';
        $expected->method     = 'keysend';
        $expected->suggested  = '0.00000005000';
        $expected->recipients = [$recipA, $recipB];

        $values = $feed->getPodcastIndexValues();
        $this->assertEquals($expected, $values[0]);
    }

    public function testGetsSocialInteracts(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expectedA             = new stdClass();
        $expectedA->priority   = 1;
        $expectedA->protocol   = "activitypub";
        $expectedA->uri        = "https://podcastindex.social/web/@dave/108013847520053258";
        $expectedA->accountId  = "@dave";
        $expectedA->accountUrl = "https://podcastindex.social/web/@dave";

        $expectedB             = new stdClass();
        $expectedB->priority   = 2;
        $expectedB->protocol   = "twitter";
        $expectedB->uri        = "https://twitter.com/PodcastindexOrg/status/1507120226361647115";
        $expectedB->accountId  = "@podcastindexorg";
        $expectedB->accountUrl = "https://twitter.com/PodcastindexOrg";

        $response = $feed->getPodcastIndexSocialInteracts();
        $this->assertEquals($expectedA, $response[0]);
        $this->assertEquals($expectedB, $response[1]);
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

        // using alias
        $this->assertEquals($expected, $entry->getPodcastIndexTranscript());
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

        // using alias
        $this->assertEquals($expected, $entry->getPodcastIndexChapters());
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

        // using alias
        $this->assertEquals([
            $expected,
        ], $entry->getPodcastIndexSoundbites());
    }

    public function testGetsEntryLocation(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $expected              = new stdClass();
        $expected->description = 'Austin';
        $expected->geo         = 'geo:30.2711286,-97.7436995';
        $expected->osm         = 'R113314';
        $expected->rel         = 'subject';
        $expected->country     = 'US';

        $this->assertEquals($expected, $entry->getPodcastIndexLocation());
    }
}
