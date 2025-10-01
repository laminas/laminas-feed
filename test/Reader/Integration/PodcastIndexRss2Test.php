<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Reader\Integration;

use Laminas\Feed\Reader;
use PHPUnit\Framework\TestCase;
use stdClass;

use function count;
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

    public function testGetsFundings(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expected        = new stdClass();
        $expected->url   = 'http://example.com/donate';
        $expected->title = 'Support the show!';

        $this->assertEquals($expected, $feed->getPodcastIndexFundings()[0]);
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

        /** @psalm-suppress DeprecatedMethod */
        $this->assertEquals($expected, $feed->getPodcastIndexImages());
    }

    public function testGetsDetailedImages(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expectedA              = new stdClass();
        $expectedA->alt         = "An antenna emanating signal waves";
        $expectedA->purpose     = "artwork";
        $expectedA->type        = "image/jpeg";
        $expectedA->aspectRatio = "1/1";
        $expectedA->href        = "https://example.com/images/ep1/pci_square-massive.jpg";
        $expectedA->width       = "1400";
        $expectedA->height      = "1400";

        $expectedB              = new stdClass();
        $expectedB->alt         = "Another antenna emanating signal waves";
        $expectedB->purpose     = "artwork social";
        $expectedB->type        = "image/jpeg";
        $expectedB->aspectRatio = "16/9";
        $expectedB->href        = "https://example.com/images/ep1/pci_landscape-massive_wide.jpg";
        $expectedB->width       = "";
        $expectedB->height      = "";

        $images = $feed->getPodcastIndexDetailedImages();
        $this->assertEquals($expectedA, $images[0]);
        $this->assertEquals($expectedB, $images[1]);
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

        $expected                  = new stdClass();
        $expected->type            = 'lightning';
        $expected->method          = 'keysend';
        $expected->suggested       = '0.00000005000';
        $expected->valueRecipients = [$recipA, $recipB];

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

    public function testGetsChat(): void
    {
        /** @var Reader\Extension\PodcastIndex\Feed $feed */
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expected            = new stdClass();
        $expected->server    = 'irc.zeronode.net';
        $expected->protocol  = 'irc';
        $expected->accountId = '@jsmith';
        $expected->space     = '#myawesomepodcast';

        $actual = $feed->getPodcastIndexChat();
        $this->assertEquals($expected, $actual);
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

    public function testGetsEntryFundings(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $expected        = new stdClass();
        $expected->url   = 'http://example.com/item/donate';
        $expected->title = 'Support the first Episode!';

        $this->assertEquals($expected, $entry->getPodcastIndexFundings()[0]);
    }

    public function testGetsEntryLicense(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $expected             = new stdClass();
        $expected->identifier = 'my-podcast-license-v1';
        $expected->url        = 'https://example.org/mypodcastlicense/full.pdf';

        $this->assertEquals($expected, $entry->getPodcastIndexLicense());
    }

    public function testGetsEntryPeople(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $expected        = new stdClass();
        $expected->name  = 'James Brown';
        $expected->role  = 'guest';
        $expected->group = 'writing';
        $expected->img   = 'http://example.com/images/alicebrown.jpg';
        $expected->href  = 'https://www.wikipedia/alicebrown';

        // using "people"
        $people = $entry->getPodcastIndexPeople();
        $this->assertEquals($expected, $people[0]);

        // using "persons"
        $persons = $entry->getPodcastIndexPersons();
        $this->assertEquals($expected, $persons[0]);
    }

    public function testGetsEntryTxts(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $expectedA          = new stdClass();
        $expectedA->value   = 'S6lpp-7ZCn8-dZfGc-OoyaG';
        $expectedA->purpose = 'verify';

        $expectedB          = new stdClass();
        $expectedB->value   = '05124';
        $expectedB->purpose = 'applepodcastsverify';

        $txts = $entry->getPodcastIndexTxts();
        $this->assertEquals($expectedA, $txts[0]);
        $this->assertEquals($expectedB, $txts[1]);
    }

    public function testGetsEntrySocialInteracts(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

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

        $response = $entry->getPodcastIndexSocialInteracts();
        $this->assertEquals($expectedA, $response[0]);
        $this->assertEquals($expectedB, $response[1]);
    }

    public function testGetsEntryValuesWithTimeSplitRecipients(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        // prepare value recipients as child elements of value time split

        $timeSplitRecipientA              = new stdClass();
        $timeSplitRecipientA->name        = "Alice (Podcaster)";
        $timeSplitRecipientA->type        = "node";
        $timeSplitRecipientA->address     = "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52";
        $timeSplitRecipientA->split       = '80';
        $timeSplitRecipientA->customKey   = '';
        $timeSplitRecipientA->customValue = '';
        $timeSplitRecipientA->fee         = '';

        $timeSplitRecipientB              = new stdClass();
        $timeSplitRecipientB->name        = "Malcolm (Guest)";
        $timeSplitRecipientB->type        = "node";
        $timeSplitRecipientB->address     = "02dd306e68c46681aa21d88a436fb35355a8579dd30201581cefa17cb179fc4c15";
        $timeSplitRecipientB->split       = '20';
        $timeSplitRecipientB->customKey   = '';
        $timeSplitRecipientB->customValue = '';
        $timeSplitRecipientB->fee         = '';

        // prepare value time split as child element of value

        $valueTimeSplit                   = new stdClass();
        $valueTimeSplit->startTime        = '63';
        $valueTimeSplit->duration         = '388';
        $valueTimeSplit->remoteStartTime  = '';
        $valueTimeSplit->remotePercentage = '';
        $valueTimeSplit->valueRecipients  = [$timeSplitRecipientA, $timeSplitRecipientB];

        // prepare value recipients as child elements of value

        $valueRecipientA              = new stdClass();
        $valueRecipientA->name        = "Alice (Podcaster)";
        $valueRecipientA->type        = "node";
        $valueRecipientA->address     = "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52";
        $valueRecipientA->split       = '40';
        $valueRecipientA->customKey   = '';
        $valueRecipientA->customValue = '';
        $valueRecipientA->fee         = '';

        $valueRecipientB              = new stdClass();
        $valueRecipientB->name        = "Bob (Podcaster)";
        $valueRecipientB->type        = "node";
        $valueRecipientB->address     = "032f4ffbbafffbe51726ad3c164a3d0d37ec27bc67b29a159b0f49ae8ac21b8508";
        $valueRecipientB->split       = '60';
        $valueRecipientB->customKey   = '';
        $valueRecipientB->customValue = '';
        $valueRecipientB->fee         = '';

        // prepare value and assign recipients and value time split to it

        $expected                  = new stdClass();
        $expected->type            = 'lightning';
        $expected->method          = 'keysend';
        $expected->suggested       = '0.00000005000';
        $expected->valueRecipients = [$valueRecipientA, $valueRecipientB];
        $expected->valueTimeSplits = [$valueTimeSplit];

        $values = $entry->getPodcastIndexValues();
        $this->assertEquals($expected, $values[0]);
    }

    public function testGetsEntryValuesWithTimeSplitRemoteItems(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        // prepare value recipients as child elements of value

        $valueRecipientA              = new stdClass();
        $valueRecipientA->name        = "Alice (Podcaster)";
        $valueRecipientA->type        = "node";
        $valueRecipientA->address     = "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52";
        $valueRecipientA->split       = '40';
        $valueRecipientA->customKey   = '';
        $valueRecipientA->customValue = '';
        $valueRecipientA->fee         = '';

        $valueRecipientB              = new stdClass();
        $valueRecipientB->name        = "Bob (Podcaster)";
        $valueRecipientB->type        = "node";
        $valueRecipientB->address     = "032f4ffbbafffbe51726ad3c164a3d0d37ec27bc67b29a159b0f49ae8ac21b8508";
        $valueRecipientB->split       = '60';
        $valueRecipientB->customKey   = '';
        $valueRecipientB->customValue = '';
        $valueRecipientB->fee         = '';

        // prepare first value time split with remote item

        $remoteItemA           = new stdClass();
        $remoteItemA->itemGuid = 'https://podcastindex.org/podcast/4148683#1';
        $remoteItemA->feedGuid = 'a94f5cc9-8c58-55fc-91fe-a324087a655b';
        $remoteItemA->medium   = 'music';
        $remoteItemA->feedUrl  = '';
        $remoteItemA->title    = '';

        $valueTimeSplitA                   = new stdClass();
        $valueTimeSplitA->startTime        = '60';
        $valueTimeSplitA->duration         = '237';
        $valueTimeSplitA->remoteStartTime  = '';
        $valueTimeSplitA->remotePercentage = '95';
        $valueTimeSplitA->remoteItem       = $remoteItemA;

        // prepare second value time split with remote item

        $remoteItemB           = new stdClass();
        $remoteItemB->itemGuid = 'https://podcastindex.org/podcast/4148683#3';
        $remoteItemB->feedGuid = 'b83f5cc9-8c58-55fc-91fe-a324087a644c';
        $remoteItemB->medium   = 'music';
        $remoteItemB->feedUrl  = '';
        $remoteItemB->title    = '';

        $valueTimeSplitB                   = new stdClass();
        $valueTimeSplitB->startTime        = '330';
        $valueTimeSplitB->duration         = '53';
        $valueTimeSplitB->remoteStartTime  = '174';
        $valueTimeSplitB->remotePercentage = '95';
        $valueTimeSplitB->remoteItem       = $remoteItemB;

        // prepare value and assign recipients and value time splits to it

        $expected                  = new stdClass();
        $expected->type            = 'lightning';
        $expected->method          = 'keysend';
        $expected->suggested       = '0.00000005000';
        $expected->valueRecipients = [$valueRecipientA, $valueRecipientB];
        $expected->valueTimeSplits = [$valueTimeSplitA, $valueTimeSplitB];

        $values = $entry->getPodcastIndexValues();
        $this->assertEquals($expected, $values[1]);
    }

    public function testGetsEntrySeason(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $expected        = new stdClass();
        $expected->value = '3';
        $expected->name  = 'The Yearling - Chapter 3';

        $season = $entry->getPodcastIndexSeason();
        $this->assertEquals($expected, $season);
    }

    public function testGetsEntryEpisode(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $expected          = new stdClass();
        $expected->value   = '9';
        $expected->display = 'Day 5';

        $episode = $entry->getPodcastIndexEpisode();
        $this->assertEquals($expected, $episode);
    }

    public function testGetsEntryAlternateEnclosure(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $sourceA              = new stdClass();
        $sourceA->uri         = 'https://example.com/file-720.torrent';
        $sourceA->contentType = 'application/x-bittorrent';

        $sourceB              = new stdClass();
        $sourceB->uri         = 'ipfs://QmX33FYehk6ckGQ6g1D9D3FqZPix5JpKstKQKbaS8quUFb';
        $sourceB->contentType = '';

        $integrity        = new stdClass();
        $integrity->type  = 'sri';
        $integrity->value = 'sha384-ExVqijgYHm15PqQqdXfW95x+Rs6C+d6E/ICxyQOeFevnxNLR/wtJNrNYTjIysUBo';

        $expected            = new stdClass();
        $expected->type      = 'video/mp4';
        $expected->length    = '7924786';
        $expected->bitrate   = '511276.52';
        $expected->height    = '720';
        $expected->lang      = 'en';
        $expected->title     = 'Standard';
        $expected->rel       = '';
        $expected->codecs    = '';
        $expected->default   = 'true';
        $expected->sources   = [$sourceA, $sourceB];
        $expected->integrity = $integrity;

        $enclosure = $entry->getPodcastIndexAlternateEnclosures();
        $this->assertEquals($expected, $enclosure[0]);
    }

    public function testGetsEntryDetailedImages(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $expectedA              = new stdClass();
        $expectedA->alt         = "An antenna emanating signal waves";
        $expectedA->purpose     = "artwork";
        $expectedA->type        = "image/jpeg";
        $expectedA->aspectRatio = "1/1";
        $expectedA->href        = "https://example.com/images/ep1/pci_square-massive.jpg";
        $expectedA->width       = "1400";
        $expectedA->height      = "1400";

        $expectedB              = new stdClass();
        $expectedB->alt         = "Another antenna emanating signal waves";
        $expectedB->purpose     = "artwork social";
        $expectedB->type        = "image/jpeg";
        $expectedB->aspectRatio = "16/9";
        $expectedB->href        = "https://example.com/images/ep1/pci_landscape-massive_wide.jpg";
        $expectedB->width       = "";
        $expectedB->height      = "";

        $images = $entry->getPodcastIndexDetailedImages();
        $this->assertEquals($expectedA, $images[0]);
        $this->assertEquals($expectedB, $images[1]);
    }

    public function testGetsEntryContentLinks(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\Entry $entry */
        $entry = $feed->current();

        $expectedA              = new stdClass();
        $expectedA->href        = 'https://youtube.com/pc20/livestream';
        $expectedA->description = 'YouTube!';

        $contentLinks = $entry->getPodcastIndexContentLinks();
        $this->assertEquals(1, count($contentLinks));
        $this->assertEquals($expectedA, $contentLinks[0]);
    }

    public function testGetsLiveItemWithAttributes(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\LiveItem $liveItem */
        $liveItem = $feed->getPodcastIndexLiveItems()[0];

        $this->assertEquals('live', $liveItem->getStatus());
        $this->assertEquals('2021-09-26T07:30:00.000-0600', $liveItem->getStart());
        $this->assertEquals('2021-09-26T09:30:00.000-0600', $liveItem->getEnd());
    }

    public function testGetsLiveItemWithClassicChildren(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\LiveItem $liveItem */
        $liveItem = $feed->getPodcastIndexLiveItems()[0];

        $title       = 'Podcasting 2.0 Live Show';
        $description = 'A look into the future of podcasting and how we get to Podcasting 2.0!';
        $link        = 'https://example.com/podcast/live';
        $guid        = 'https://example.com/live';
        $author      = 'John Doe (john@example.com)';

        $this->assertEquals($title, $liveItem->getTitle());
        $this->assertEquals($description, $liveItem->getDescription());
        $this->assertEquals($link, $liveItem->getLink());
        $this->assertEquals($guid, $liveItem->getId());
        $this->assertEquals($author, $liveItem->getAuthor()['email']);
    }

    public function testGetsLiveItemEnclosure(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        $expected         = new stdClass();
        $expected->url    = "https://example.com/pc20/livestream?format=.mp3";
        $expected->length = "312";
        $expected->type   = "audio/mpeg";

        /** @var Reader\Extension\PodcastIndex\LiveItem $liveItem */
        $liveItem = $feed->getPodcastIndexLiveItems()[0];

        $this->assertEquals($expected, $liveItem->getEnclosure());
    }

    public function testGetsLiveItemPersons(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\LiveItem $liveItem */
        $liveItem = $feed->getPodcastIndexLiveItems()[0];

        $personA        = new stdClass();
        $personA->name  = 'Adam Curry';
        $personA->img   = 'https://example.com/images/adamcurry.jpg';
        $personA->href  = 'https://www.podchaser.com/creators/adam-curry-107ZzmWE5f';
        $personA->role  = '';
        $personA->group = '';

        $personB        = new stdClass();
        $personB->name  = 'Dave Jones';
        $personB->role  = 'guest';
        $personB->img   = 'https://example.com/images/davejones.jpg';
        $personB->href  = 'https://github.com/daveajones/';
        $personB->group = '';

        $people = $liveItem->getPodcastIndexPeople();
        $this->assertNotNull($people);
        $this->assertEquals($personA, $people[0]);
        $this->assertEquals($personB, $people[1]);
    }

    public function testGetsLiveItemAlternateEnclosure(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\LiveItem $liveItem */
        $liveItem = $feed->getPodcastIndexLiveItems()[0];

        $source              = new stdClass();
        $source->uri         = 'https://example.com/pc20/livestream';
        $source->contentType = '';

        $expected          = new stdClass();
        $expected->type    = 'audio/mpeg';
        $expected->length  = '312';
        $expected->default = 'true';
        $expected->bitrate = '';
        $expected->height  = '';
        $expected->lang    = '';
        $expected->title   = '';
        $expected->rel     = '';
        $expected->codecs  = '';
        $expected->sources = [$source];

        $enclosure = $liveItem->getPodcastIndexAlternateEnclosures();
        $this->assertEquals($expected, $enclosure[0]);
    }

    public function testGetsLiveItemContentLinks(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\LiveItem $liveItem */
        $liveItem = $feed->getPodcastIndexLiveItems()[0];

        $expectedA              = new stdClass();
        $expectedA->href        = 'https://youtube.com/pc20/livestream';
        $expectedA->description = 'YouTube!';

        $expectedB              = new stdClass();
        $expectedB->href        = 'https://twitch.com/pc20/livestream';
        $expectedB->description = 'Twitch!';

        $contentLinks = $liveItem->getPodcastIndexContentLinks();
        $this->assertEquals(2, count($contentLinks));
        $this->assertEquals($expectedA, $contentLinks[0]);
        $this->assertEquals($expectedB, $contentLinks[1]);
    }

    public function testGetsLiveItemFundings(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );

        /** @var Reader\Extension\PodcastIndex\LiveItem $liveItem */
        $liveItem = $feed->getPodcastIndexLiveItems()[0];

        $expected        = new stdClass();
        $expected->url   = 'http://example.com/live-item/donate';
        $expected->title = 'Support the live Episode!';

        $this->assertEquals($expected, $liveItem->getPodcastIndexFundings()[0]);
    }
}
