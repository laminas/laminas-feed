<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Writer\Extension\PodcastIndex;

use DateTime;
use Laminas\Feed\Reader\Extension\PodcastIndex\Feed;
use Laminas\Feed\Writer;
use PHPUnit\Framework\TestCase;

use function count;
use function implode;
use function in_array;
use function time;

/**
 * @psalm-import-type PersonObject from Feed
 * @psalm-import-type RemoteItemObject from Feed
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
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLocked($locked);
    }

    /**
     * @psalm-return array<string, array{0: mixed}>
     */
    public static function nonAlphaValues(): array
    {
        return [
            'null'       => [null],
            'zero'       => [0],
            'int'        => [1],
            'zero-float' => [0.0],
            'float'      => [1.1],
            'string'     => ['1'],
            'array'      => [['yes']],
            'object'     => [(object) ['value' => 'yes']],
        ];
    }

    /**
     * @dataProvider nonAlphaValues
     * @param mixed $value
     */
    public function testSetLockedThrowsExceptionOnNonAlphaValue($value): void
    {
        $feed = new Writer\Feed();

        $locked = [
            'value' => $value,
            'owner' => 'john.doe@example.com',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLocked($locked);
    }

    public function testSetFunding(): void
    {
        $feed = new Writer\Feed();

        $funding = [
            'title' => 'Support the show!',
            'url'   => 'http://example.com/donate',
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
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexFunding($locked);
    }

    public function testSetLicense(): void
    {
        $feed = new Writer\Feed();

        $license = [
            'identifier' => 'cc-by-4.0',
            'url'        => 'https://spdx.org/licenses/CC-BY-4.0.html',
        ];
        $feed->setPodcastIndexLicense($license);
        $this->assertEquals($license, $feed->getPodcastIndexLicense());
    }

    public function testSetLicenseThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $license = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLicense($license);
    }

    public function testSetLicenseThrowsExceptionOnInvalidIdentifier(): void
    {
        $feed = new Writer\Feed();

        $license = [
            'identifier' => 1234,
            'url'        => 'https://spdx.org/licenses/CC-BY-4.0.html',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLicense($license);
    }

    public function testSetLicenseThrowsExceptionOnInvalidUrl(): void
    {
        $feed = new Writer\Feed();

        $license = [
            'identifier' => 'cc-by-4.0',
            'url'        => 'spdx.org/licenses/CC-BY-4.0.html',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLicense($license);
    }

    public function testSetLicenseThrowsExceptionOnMissingUrl(): void
    {
        $feed = new Writer\Feed();

        $license = [
            'identifier' => 'cc-by-4.0',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLicense($license);
    }

    public function testSetLocation(): void
    {
        $feed = new Writer\Feed();

        $location = [
            'description' => 'London, Baker Street',
            'geo'         => 'geo:-27.86159,153.3169',
            'osm'         => 'W43678282',
        ];
        $feed->setPodcastIndexLocation($location);
        $this->assertEquals($location, $feed->getPodcastIndexLocation());
    }

    public function testSetLocationWithOneArgument(): void
    {
        $feed = new Writer\Feed();

        $location = [
            'description' => 'London, Baker Street',
        ];
        $feed->setPodcastIndexLocation($location);
        $this->assertEquals($location, $feed->getPodcastIndexLocation());
    }

    public function testSetLocationThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $location = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLocation($location);
    }

    public function testSetLocationThrowsExceptionOnInvalidGeo(): void
    {
        $feed = new Writer\Feed();

        $location = [
            'description' => 'London, Baker Street',
            'geo'         => [-27.86159, 153.3169],
            'osm'         => 'W43678282',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLocation($location);
    }

    public function testSetLocationThrowsExceptionOnInvalidOsm(): void
    {
        $feed = new Writer\Feed();

        $location = [
            'description' => 'London, Baker Street',
            'geo'         => 'geo:-27.86159,153.3169',
            'osm'         => false,
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLocation($location);
    }

    public function testSetImages(): void
    {
        $feed = new Writer\Feed();

        $srcset = [
            "https://example.com/images/ep1/pci_avatar-massive.jpg 1500w",
            "https://example.com/images/ep1/pci_avatar-middle.jpg 600w",
            "https://example.com/images/ep1/pci_avatar-small.jpg 300w",
            "https://example.com/images/ep1/pci_avatar-tiny.jpg 150w",
        ];
        $images = [
            'srcset' => implode(", ", $srcset), // cast to string
        ];

        $feed->setPodcastIndexImages($images);
        $this->assertEquals($images, $feed->getPodcastIndexImages());
    }

    public function testSetImagesThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $images = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexImages($images);
    }

    public function testSetImagesThrowsExceptionOnInvalidSrcsetType(): void
    {
        $feed = new Writer\Feed();

        $srcset = [
            "https://example.com/images/ep1/pci_avatar-massive.jpg 1500w",
            "https://example.com/images/ep1/pci_avatar-middle.jpg 600w",
            "https://example.com/images/ep1/pci_avatar-small.jpg 300w",
            "https://example.com/images/ep1/pci_avatar-tiny.jpg 150w",
        ];
        $images = [
            'srcset' => $srcset, // plain array, not allowed
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexImages($images);
    }

    public function testSetUpdateFrequency(): void
    {
        $date = new DateTime();
        $feed = new Writer\Feed();

        $updateFrequency = [
            'description' => 'Daily',
            'complete'    => false,
            'dtstart'     => $date,
            'rrule'       => 'FREQ=DAILY',
        ];
        $feed->setPodcastIndexUpdateFrequency($updateFrequency);
        $this->assertEquals($updateFrequency, $feed->getPodcastIndexUpdateFrequency());
    }

    public function testSetUpdateFrequencyWithOneArgument(): void
    {
        $feed = new Writer\Feed();

        $updateFrequency = [
            'description' => 'Daily',
        ];
        $feed->setPodcastIndexUpdateFrequency($updateFrequency);
        $this->assertEquals($updateFrequency, $feed->getPodcastIndexUpdateFrequency());
    }

    public function testSetUpdateFrequencyThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $updateFrequency = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexUpdateFrequency($updateFrequency);
    }

    public function testSetUpdateFrequencyThrowsExceptionOnInvalidCompleteValue(): void
    {
        $feed = new Writer\Feed();

        $updateFrequency = [
            'description' => 'Daily',
            'complete'    => 'yes',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexUpdateFrequency($updateFrequency);
    }

    public function testSetUpdateFrequencyThrowsExceptionOnInvalidDateValue(): void
    {
        $feed = new Writer\Feed();

        $updateFrequency = [
            'description' => 'Daily',
            'dtstart'     => time(),
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexUpdateFrequency($updateFrequency);
    }

    public function testAddPerson(): void
    {
        $feed = new Writer\Feed();

        $person = [
            'name'  => 'Hercules Poirot',
            'role'  => 'guest',
            'group' => 'starring',
            'img'   => 'https://poirot.com/about/my-moustage.jpg',
            'href'  => 'https://poirot.com/my-cases',
        ];
        $feed->addPodcastIndexPerson($person);

        /** @var list<PersonObject> $people */
        $people = $feed->getPodcastIndexPeople();
        $this->assertTrue(in_array($person, $people));
    }

    public function testSetPeople(): void
    {
        $feed = new Writer\Feed();

        $people = [
            [
                'name'  => 'Hercules Poirot',
                'role'  => 'guest',
                'group' => 'starring',
                'img'   => 'https://poirot.com/about/my-moustage.jpg',
                'href'  => 'https://poirot.com/my-cases',
            ],
            [
                'name'  => 'Agatha Christie',
                'role'  => 'guest',
                'group' => 'writing',
            ],
        ];
        // set
        $feed->setPodcastIndexPeople($people);
        /** @var list<PersonObject> $peopleSaved */
        $peopleSaved = $feed->getPodcastIndexPeople();
        foreach ($people as $person) {
            $this->assertTrue(in_array($person, $peopleSaved));
        }
        // update
        $newPersons = [
            [
                'name'  => 'Alice Brown',
                'role'  => 'guest',
                'group' => 'writing',
                'img'   => 'http://example.com/images/alicebrown.jpg',
                'href'  => 'https://www.wikipedia/alicebrown',
            ],
        ];
        $feed->setPodcastIndexPeople($newPersons);
        /** @var list<PersonObject> $updated */
        $updated = $feed->getPodcastIndexPeople();
        $this->assertEquals(1, count($updated));
        $this->assertEquals($newPersons, $updated);

        // delete
        $feed->setPodcastIndexPeople();
        $this->assertNull($feed->getPodcastIndexPeople());
    }

    public function testAddPersonWithOneArgument(): void
    {
        $feed = new Writer\Feed();

        $person = [
            'name' => 'Hercules Poirot',
        ];
        $feed->addPodcastIndexPerson($person);

        /** @var list<PersonObject> $people */
        $people = $feed->getPodcastIndexPeople();
        $this->assertTrue(in_array($person, $people));
    }

    public function testSetPersonThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $person = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->addPodcastIndexPerson($person);
    }

    public function testSetPersonThrowsExceptionOnInvalidImageUrl(): void
    {
        $feed = new Writer\Feed();

        $person = [
            'name'  => 'Hercules Poirot',
            'role'  => 'guest',
            'group' => 'writing',
            'img'   => 'poirot.com/my-moustage.jpg',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->addPodcastIndexPerson($person);
    }

    public function testSetTrailer(): void
    {
        $feed = new Writer\Feed();

        $trailer = [
            'title'   => 'Season 4: Race for the Clouds',
            'pubdate' => "Thu, 01 Apr 2021 08:00:00 EST",
            'url'     => "https://example.org/season4teaser.mp4",
            'length'  => 12345678,
            'type'    => "video/mp4",
            'season'  => 4,
        ];

        $feed->setPodcastIndexTrailer($trailer);
        $this->assertEquals($trailer, $feed->getPodcastIndexTrailer());
    }

    public function testSetTrailerWithRequiredArguments(): void
    {
        $feed = new Writer\Feed();

        $trailer = [
            'title'   => 'Season 4: Race for the Clouds',
            'pubdate' => "Thu, 01 Apr 2021 08:00:00 EST",
            'url'     => "https://example.org/season4teaser.mp4",
        ];

        $feed->setPodcastIndexTrailer($trailer);
        $this->assertEquals($trailer, $feed->getPodcastIndexTrailer());
    }

    public function testSetTrailerThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $trailer = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexTrailer($trailer);
    }

    public function testSetLocationThrowsExceptionOnInvalidUrl(): void
    {
        $feed = new Writer\Feed();

        $trailer = [
            'title'   => 'Season 4: Race for the Clouds',
            'pubdate' => "Thu, 01 Apr 2021 08:00:00 EST",
            'url'     => "example.org/season4teaser.mp4",
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexTrailer($trailer);
    }

    public function testSetGuid(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'value' => '917393e3-1b1e-5cef-ace4-edaa54e1f810',
        ];

        $feed->setPodcastIndexGuid($data);
        $this->assertEquals($data, $feed->getPodcastIndexGuid());
    }

    public function testSetGuidThrowsExceptionOnInvalidArgument(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexGuid($data);
    }

    public function testSetMedium(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'value' => 'audiobook',
        ];

        $feed->setPodcastIndexMedium($data);
        $this->assertEquals($data, $feed->getPodcastIndexMedium());
    }

    public function testSetMediumThrowsExceptionOnInvalidArgument(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexMedium($data);
    }

    public function testAddBlock(): void
    {
        $feed = new Writer\Feed();

        $block = [
            'value' => 'yes',
            'id'    => 'google',
        ];
        $feed->addPodcastIndexBlock($block);

        /** @var list<array{value: string, id?: string}> $blocks */
        $blocks = $feed->getPodcastIndexBlocks();
        $this->assertTrue(in_array($block, $blocks));
    }

    public function testSetBlocks(): void
    {
        $feed = new Writer\Feed();

        $blocks = [
            [
                'value' => 'no',
                'id'    => '',
            ],
            [
                'value' => 'yes',
                'id'    => 'google',
            ],
        ];

        // set
        $feed->setPodcastIndexBlocks($blocks);
        /** @var list<object{value: string, id?: string}> $blocksSaved */
        $blocksSaved = $feed->getPodcastIndexBlocks();
        foreach ($blocks as $block) {
            $this->assertTrue(in_array($block, $blocksSaved));
        }

        // add
        $singleBlock = [
            'value' => 'yes',
            'id'    => 'apple',
        ];
        $feed->addPodcastIndexBlock($singleBlock);
        /** @psalm-var list<object{value: string, id?: string}> $moreBlocksSaved */
        $moreBlocksSaved = $feed->getPodcastIndexBlocks();
        foreach ($blocks as $block) {
            $this->assertTrue(in_array($block, $moreBlocksSaved));
        }
        $this->assertTrue(in_array($singleBlock, $moreBlocksSaved));

        // update
        $newBlocks = [
            [
                'value' => 'no',
                'id'    => 'google',
            ],
        ];
        $feed->setPodcastIndexBlocks($newBlocks);
        /** @var list<object{value: string, id?: string}> $updated */
        $updated = $feed->getPodcastIndexBlocks();
        $this->assertEquals(1, count($updated));
        $this->assertEquals($newBlocks, $updated);

        // delete
        $feed->setPodcastIndexBlocks();
        $this->assertNull($feed->getPodcastIndexBlocks());
    }

    public function testAddBlockWithOneArgument(): void
    {
        $feed = new Writer\Feed();

        $block = [
            'value' => 'yes',
        ];
        $feed->addPodcastIndexBlock($block);

        /** @psalm-var list<object{value: string, id?: string}> $blocks */
        $blocks = $feed->getPodcastIndexBlocks();
        $this->assertTrue(in_array($block, $blocks));
    }

    public function testAddBlockThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->addPodcastIndexBlock($data);
    }

    public function testAddBlockThrowsExceptionOnInvalidValue(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'value' => true,
            'id'    => 'google',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->addPodcastIndexBlock($data);
    }

    public function testAddTxt(): void
    {
        $feed = new Writer\Feed();

        $txt = [
            'value'   => 'S6lpp-7ZCn8-dZfGc-OoyaG',
            'purpose' => 'verify',
        ];
        $feed->addPodcastIndexTxt($txt);

        /** @var list<array{value: string, purpose?: string}> $txts */
        $txts = $feed->getPodcastIndexTxts();
        $this->assertTrue(in_array($txt, $txts));
    }

    public function testSetTxts(): void
    {
        $feed = new Writer\Feed();

        $txts = [
            [
                'value'   => 'S6lpp-7ZCn8-dZfGc-OoyaG',
                'purpose' => 'verify',
            ],
            [
                'value'   => '2022-10-26T04:45:30.742Z',
                'purpose' => 'release',
            ],
        ];

        // set
        $feed->setPodcastIndexTxts($txts);
        /** @var list<object{value: string, purpose?: string}> $txtsSaved */
        $txtsSaved = $feed->getPodcastIndexTxts();
        foreach ($txts as $txt) {
            $this->assertTrue(in_array($txt, $txtsSaved));
        }

        // add
        $singleTxt = [
            'value'   => 'naj3eEZaWVVY9a38uhX8FekACyhtqP4JN',
            'purpose' => 'verify',
        ];
        $feed->addPodcastIndexTxt($singleTxt);
        /** @psalm-var list<object{value: string, purpose?: string}> $moreTxtsSaved */
        $moreTxtsSaved = $feed->getPodcastIndexTxts();
        foreach ($txts as $txt) {
            $this->assertTrue(in_array($txt, $moreTxtsSaved));
        }
        $this->assertTrue(in_array($singleTxt, $moreTxtsSaved));

        // update
        $newTxts = [
            [
                'value'   => '05124',
                'purpose' => 'applepodcastsverify',
            ],
        ];
        $feed->setPodcastIndexTxts($newTxts);
        /** @var list<object{value: string, purpose?: string}> $updated */
        $updated = $feed->getPodcastIndexTxts();
        $this->assertEquals(1, count($updated));
        $this->assertEquals($newTxts, $updated);

        // delete
        $feed->setPodcastIndexTxts();
        $this->assertNull($feed->getPodcastIndexTxts());
    }

    public function testAddTxtWithOneArgument(): void
    {
        $feed = new Writer\Feed();

        $txt = [
            'value' => 'naj3eEZaWVVY9a38uhX8FekACyhtqP4JN',
        ];
        $feed->addPodcastIndexTxt($txt);

        /** @psalm-var list<object{value: string, purpose?: string}> $txts */
        $txts = $feed->getPodcastIndexTxts();
        $this->assertTrue(in_array($txt, $txts));
    }

    public function testAddTxtThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->addPodcastIndexTxt($data);
    }

    public function testAddTxtThrowsExceptionOnInvalidValue(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'value'   => true,
            'purpose' => 'google',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->addPodcastIndexTxt($data);
    }

    public function testSetPodping(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'usesPodping' => true,
        ];

        $feed->setPodcastIndexPodping($data);
        $this->assertEquals($data, $feed->getPodcastIndexPodping());
    }

    public function testSetPodpingThrowsExceptionOnInvalidArgument(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexPodping($data);
    }

    public function testAddRemoteItem(): void
    {
        $feed = new Writer\Feed();

        $remoteItem = [
            'feedGuid' => "917393e3-1b1e-5cef-ace4-edaa54e1f810",
            'feedUrl'  => "https://feeds.example.org/917393e3-1b1e-5cef-ace4-edaa54e1f810/rss.xml",
            'medium'   => "podcast",
            'title'    => "Some Example",
        ];
        $feed->addPodcastIndexRemoteItem($remoteItem);

        /** @var list<RemoteItemObject> $remoteItems */
        $remoteItems = $feed->getPodcastIndexRemoteItems();
        $this->assertTrue(in_array($remoteItem, $remoteItems));
    }

    public function testSetRemoteItems(): void
    {
        $feed = new Writer\Feed();

        $remoteItems = [
            [
                'feedGuid' => "917393e3-1b1e-5cef-ace4-edaa54e1f810",
                'feedUrl'  => "https://feeds.example.org/917393e3-1b1e-5cef-ace4-edaa54e1f810/rss.xml",
                'medium'   => "podcast",
                'title'    => "Some Example",
            ],
            [
                'feedGuid' => "917393e3-xxxx-yyyy-ace4-edaa54e1f810",
                'feedUrl'  => "https://feeds.other-example.org/917393e3-xxxx-yyyy-ace4-edaa54e1f810/rss.xml",
                'medium'   => "podcast",
                'title'    => "Some Other Example",
            ],
        ];

        // set
        $feed->setPodcastIndexRemoteItems($remoteItems);
        /** @var list<RemoteItemObject> $remoteItemsSaved */
        $remoteItemsSaved = $feed->getPodcastIndexRemoteItems();
        foreach ($remoteItems as $remoteItem) {
            $this->assertTrue(in_array($remoteItem, $remoteItemsSaved));
        }

        // add
        $singleRemoteItem = [
            'feedGuid' => "917393e3-xxxx-very-news-edaa54e1f810",
            'feedUrl'  => "https://feeds.new-other-example.org/917393e3-xxxx-news-ace4-edaa54e1f810/rss.xml",
            'medium'   => "podcast",
            'title'    => "New Other Example",
        ];
        $feed->addPodcastIndexRemoteItem($singleRemoteItem);
        /** @psalm-var list<RemoteItemObject> $moreRemoteItemsSaved */
        $moreRemoteItemsSaved = $feed->getPodcastIndexRemoteItems();
        foreach ($remoteItems as $remoteItem) {
            $this->assertTrue(in_array($remoteItem, $moreRemoteItemsSaved));
        }
        $this->assertTrue(in_array($singleRemoteItem, $moreRemoteItemsSaved));

        // update
        $newRemoteItems = [
            [
                'feedGuid' => "917393e3-some-thing-else-edaa54e1f810",
                'feedUrl'  => "https://feeds.other.org/edaa54e1f810/rss.xml",
                'medium'   => "podcast",
                'title'    => "Something Else",
            ],
        ];
        $feed->setPodcastIndexRemoteItems($newRemoteItems);
        /** @var list<RemoteItemObject> $updated */
        $updated = $feed->getPodcastIndexRemoteItems();
        $this->assertEquals(1, count($updated));
        $this->assertEquals($newRemoteItems, $updated);

        // delete
        $feed->setPodcastIndexRemoteItems();
        $this->assertNull($feed->getPodcastIndexRemoteItems());
    }

    public function testAddRemoteItemWithOneArgument(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'feedGuid' => "917393e3-xxxx-very-news-edaa54e1f810",
        ];
        $feed->addPodcastIndexRemoteItem($data);

        /** @psalm-var list<RemoteItemObject> $items */
        $items = $feed->getPodcastIndexRemoteItems();
        $this->assertTrue(in_array($data, $items));
    }

    public function testAddRemoteItemThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->addPodcastIndexRemoteItem($data);
    }

    public function testAddRemoteItemThrowsExceptionOnInvalidValue(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'feedGuid' => "917393e3-zzzz-yyyy-gggg-edaa54e1f810",
            'feedUrl'  => 'www.google.com',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->addPodcastIndexRemoteItem($data);
    }

    public function testAddPodrollRemoteItem(): void
    {
        $feed = new Writer\Feed();

        $remoteItem = [
            'feedGuid' => "917393e3-1b1e-5cef-ace4-edaa54e1f810",
            'feedUrl'  => "https://feeds.example.org/917393e3-1b1e-5cef-ace4-edaa54e1f810/rss.xml",
            'medium'   => "podcast",
            'title'    => "Some Example",
        ];
        $feed->addPodcastIndexPodrollRemoteItem($remoteItem);

        /** @var list<RemoteItemObject> $remoteItems */
        $remoteItems = $feed->getPodcastIndexPodroll();
        $this->assertTrue(in_array($remoteItem, $remoteItems));
    }

    public function testSetPodroll(): void
    {
        $feed = new Writer\Feed();

        $remoteItems = [
            [
                'feedGuid' => "917393e3-1b1e-5cef-ace4-edaa54e1f810",
                'feedUrl'  => "https://feeds.example.org/917393e3-1b1e-5cef-ace4-edaa54e1f810/rss.xml",
                'medium'   => "podcast",
                'title'    => "Some Example",
            ],
            [
                'feedGuid' => "917393e3-xxxx-yyyy-ace4-edaa54e1f810",
                'feedUrl'  => "https://feeds.other-example.org/917393e3-xxxx-yyyy-ace4-edaa54e1f810/rss.xml",
                'medium'   => "podcast",
                'title'    => "Some Other Example",
            ],
        ];

        // set
        $feed->setPodcastIndexPodroll($remoteItems);
        /** @var list<RemoteItemObject> $remoteItemsSaved */
        $remoteItemsSaved = $feed->getPodcastIndexPodroll();
        foreach ($remoteItems as $remoteItem) {
            $this->assertTrue(in_array($remoteItem, $remoteItemsSaved));
        }

        // add
        $singleRemoteItem = [
            'feedGuid' => "917393e3-xxxx-very-news-edaa54e1f810",
            'feedUrl'  => "https://feeds.new-other-example.org/917393e3-xxxx-news-ace4-edaa54e1f810/rss.xml",
            'medium'   => "podcast",
            'title'    => "New Other Example",
        ];
        $feed->addPodcastIndexPodrollRemoteItem($singleRemoteItem);
        /** @psalm-var list<RemoteItemObject> $moreRemoteItemsSaved */
        $moreRemoteItemsSaved = $feed->getPodcastIndexPodroll();
        foreach ($remoteItems as $remoteItem) {
            $this->assertTrue(in_array($remoteItem, $moreRemoteItemsSaved));
        }
        $this->assertTrue(in_array($singleRemoteItem, $moreRemoteItemsSaved));

        // update
        $newRemoteItems = [
            [
                'feedGuid' => "917393e3-some-thing-else-edaa54e1f810",
                'feedUrl'  => "https://feeds.other.org/edaa54e1f810/rss.xml",
                'medium'   => "podcast",
                'title'    => "Something Else",
            ],
        ];
        $feed->setPodcastIndexPodroll($newRemoteItems);
        /** @var list<RemoteItemObject> $updated */
        $updated = $feed->getPodcastIndexPodroll();
        $this->assertEquals(1, count($updated));
        $this->assertEquals($newRemoteItems, $updated);

        // delete
        $feed->setPodcastIndexPodroll();
        $this->assertNull($feed->getPodcastIndexPodroll());
    }

    public function testAddPodrollRemoteItemWithOneArgument(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'feedGuid' => "917393e3-xxxx-very-news-edaa54e1f810",
        ];
        $feed->addPodcastIndexPodrollRemoteItem($data);

        /** @psalm-var list<RemoteItemObject> $items */
        $items = $feed->getPodcastIndexPodroll();
        $this->assertTrue(in_array($data, $items));
    }

    public function testAddPodrollRemoteItemThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->addPodcastIndexPodrollRemoteItem($data);
    }

    public function testAddPodrollRemoteItemThrowsExceptionOnInvalidValue(): void
    {
        $feed = new Writer\Feed();

        $data = [
            'feedGuid' => "917393e3-zzzz-yyyy-gggg-edaa54e1f810",
            'feedUrl'  => 'www.google.com',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->addPodcastIndexPodrollRemoteItem($data);
    }
}
