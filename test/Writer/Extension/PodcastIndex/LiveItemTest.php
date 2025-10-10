<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Writer\Extension\PodcastIndex;

use Laminas\Feed\Reader\Extension\PodcastIndex\AttributesReader;
use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Extension\PodcastIndex;
use PHPUnit\Framework\TestCase;

use function count;
use function in_array;

/**
 * @psalm-import-type LicenseObject from AttributesReader
 * @psalm-import-type LocationObject from AttributesReader
 * @psalm-import-type BlockObject from AttributesReader
 * @psalm-import-type TxtObject from AttributesReader
 * @psalm-import-type PersonObject from AttributesReader
 * @psalm-import-type UpdateFrequencyObject from AttributesReader
 * @psalm-import-type TrailerObject from AttributesReader
 * @psalm-import-type RemoteItemObject from AttributesReader
 * @psalm-import-type ValueRecipientObject from AttributesReader
 * @psalm-import-type ValueTimeSplitObject from AttributesReader
 * @psalm-import-type ValueObject from AttributesReader
 * @psalm-import-type DetailedImageObject from AttributesReader
 * @psalm-import-type SocialInteractObject from AttributesReader
 * @psalm-import-type TranscriptObject from AttributesReader
 * @psalm-import-type ChaptersObject from AttributesReader
 * @psalm-import-type SoundbiteObject from AttributesReader
 * @psalm-import-type SeasonObject from AttributesReader
 * @psalm-import-type EpisodeObject from AttributesReader
 * @psalm-import-type SourceObject from AttributesReader
 * @psalm-import-type IntegrityObject from AttributesReader
 * @psalm-import-type AlternateEnclosureObject from AttributesReader
 * @psalm-import-type ContentLinkObject from AttributesReader
 */
class LiveItemTest extends TestCase
{
    protected PodcastIndex\LiveItem $liveItem;

    protected function setUp(): void
    {
        $feeWriter = new Writer\Feed();

        $liveItem = [
            'status' => 'live',
            'start'  => '2021-09-26T07:30:00.000-0600',
            'end'    => '2021-09-26T09:30:00.000-0600',
        ];

        /** @psalm-var PodcastIndex\LiveItem $this->liveItem */
        $this->liveItem = $feeWriter->createPodcastIndexLiveItem($liveItem);
    }

    protected function tearDown(): void
    {
        Writer\Writer::reset();
    }

    public function testAddsAuthor(): void
    {
        $this->liveItem->addAuthor([
            'name'  => 'Joe',
            'email' => 'joe@example.com',
        ]);
        $this->assertEquals([
            [
                'name'  => 'Joe',
                'email' => 'joe@example.com',
            ],
        ], $this->liveItem->getAuthors());
    }

    public function testAddsAuthorsFromArrayOfAuthors(): void
    {
        $this->liveItem->addAuthors([
            [
                'name' => 'Joe',
                'uri'  => 'http://www.example.com',
            ],
            [
                'name' => 'Jane',
                'uri'  => 'http://www.example.com',
            ],
        ]);
        $expected = [
            [
                'name' => 'Joe',
                'uri'  => 'http://www.example.com',
            ],
            [
                'name' => 'Jane',
                'uri'  => 'http://www.example.com',
            ],
        ];
        $this->assertEquals($expected, $this->liveItem->getAuthors());
    }

    public function testAddsEnclosure(): void
    {
        $this->liveItem->setEnclosure([
            'type'   => 'audio/mpeg',
            'uri'    => 'http://example.com/audio.mp3',
            'length' => '1337',
        ]);
        $expected = [
            'type'   => 'audio/mpeg',
            'uri'    => 'http://example.com/audio.mp3',
            'length' => '1337',
        ];
        $this->assertEquals($expected, $this->liveItem->getEnclosure());
    }

    public function testSetsId(): void
    {
        $this->liveItem->setId('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $this->liveItem->getId());
    }

    public function testSetsLink(): void
    {
        $this->liveItem->setLink('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $this->liveItem->getLink());
    }

    public function testSetsTitle(): void
    {
        $this->liveItem->setTitle('abc');
        $this->assertEquals('abc', $this->liveItem->getTitle());
    }

    public function testSetsDescription(): void
    {
        $this->liveItem->setDescription('abc');
        $this->assertEquals('abc', $this->liveItem->getDescription());
    }

    public function testSetTranscript(): void
    {
        $transcript = [
            'url'  => 'https://example.com/podcasts/everything/TranscriptEpisode3.html',
            'type' => 'text/html',
        ];
        $this->liveItem->setPodcastIndexTranscript($transcript);
        $this->assertEquals($transcript, $this->liveItem->getPodcastIndexTranscript());
    }

    public function testSetChapters(): void
    {
        $chapters = [
            'url'  => 'https://example.com/podcasts/everything/ChaptersEpisode3.json',
            'type' => 'application/json+chapters',
        ];
        $this->liveItem->setPodcastIndexChapters($chapters);
        $this->assertEquals($chapters, $this->liveItem->getPodcastIndexChapters());
    }

    public function testAddSoundbite(): void
    {
        $soundbites = [
            [
                'startTime' => '66',
                'duration'  => '39.0',
                'title'     => 'Pepper shakers comparison',
            ],
            [
                'startTime' => '112.45',
                'duration'  => '24.83',
                'title'     => 'Pepper shakers comparison',
            ],
        ];

        foreach ($soundbites as $soundbite) {
            $this->liveItem->addPodcastIndexSoundbite($soundbite);
        }
        $this->assertEquals($soundbites, $this->liveItem->getPodcastIndexSoundbites());
    }

    public function testAddLocation(): void
    {
        $location = [
            'description' => 'London, Baker Street',
            'geo'         => 'geo:-27.86159,153.3169',
            'osm'         => 'W43678282',
            'rel'         => 'subject',
            'country'     => 'GB',
        ];
        $this->liveItem->addPodcastIndexLocation($location);

        /** @var list<LocationObject> $locations */
        $locations = $this->liveItem->getPodcastIndexLocations();
        $this->assertTrue(in_array($location, $locations));
    }

    public function testAddPerson(): void
    {
        $person = [
            'name'  => 'Hercules Poirot',
            'role'  => 'guest',
            'group' => 'starring',
            'img'   => 'https://poirot.com/about/my-moustage.jpg',
            'href'  => 'https://poirot.com/my-cases',
        ];
        $this->liveItem->addPodcastIndexPerson($person);

        /** @var list<PersonObject> $people */
        $people = $this->liveItem->getPodcastIndexPeople();
        $this->assertTrue(in_array($person, $people));
    }

    public function testSetPeopleAndSetPersons(): void
    {
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
        // set using "people"
        $this->liveItem->setPodcastIndexPeople($people);
        /** @var list<PersonObject> $peopleSaved */
        $peopleSaved = $this->liveItem->getPodcastIndexPeople();
        foreach ($people as $person) {
            $this->assertTrue(in_array($person, $peopleSaved));
        }
        // update using "persons"
        $newPersons = [
            [
                'name'  => 'Alice Brown',
                'role'  => 'guest',
                'group' => 'writing',
                'img'   => 'http://example.com/images/alicebrown.jpg',
                'href'  => 'https://www.wikipedia/alicebrown',
            ],
        ];
        $this->liveItem->setPodcastIndexPersons($newPersons);
        /** @var list<PersonObject> $updated */
        $updated = $this->liveItem->getPodcastIndexPersons();
        $this->assertEquals(1, count($updated));
        $this->assertEquals($newPersons, $updated);

        // delete using "people"
        $this->liveItem->setPodcastIndexPeople();
        $this->assertNull($this->liveItem->getPodcastIndexPeople());
    }

    public function testAddTxt(): void
    {
        $txt = [
            'value'   => 'S6lpp-7ZCn8-dZfGc-OoyaG',
            'purpose' => 'verify',
        ];
        $this->liveItem->addPodcastIndexTxt($txt);

        /** @var list<array{value: string, purpose?: string}> $txts */
        $txts = $this->liveItem->getPodcastIndexTxts();
        $this->assertTrue(in_array($txt, $txts));
    }

    public function testSetTxts(): void
    {
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
        $this->liveItem->setPodcastIndexTxts($txts);
        /** @var list<object{value: string, purpose?: string}> $txtsSaved */
        $txtsSaved = $this->liveItem->getPodcastIndexTxts();
        foreach ($txts as $txt) {
            $this->assertTrue(in_array($txt, $txtsSaved));
        }

        // delete
        $this->liveItem->setPodcastIndexTxts();
        $this->assertNull($this->liveItem->getPodcastIndexTxts());
    }

    public function testSetSocialInteracts(): void
    {
        $data = [
            [
                'priority'   => 1,
                'protocol'   => "activitypub",
                'uri'        => "https://podcastindex.social/web/@dave/108013847520053258",
                'accountId'  => "@dave",
                'accountUrl' => "https://podcastindex.social/web/@dave",
            ],
            [
                'priority'   => 2,
                'protocol'   => "twitter",
                'uri'        => "https://twitter.com/PodcastindexOrg/status/1507120226361647115",
                'accountId'  => "@podcastindexorg",
                'accountUrl' => "https://twitter.com/PodcastindexOrg",
            ],
        ];
        $this->liveItem->setPodcastIndexSocialInteracts($data);

        /** @psalm-var list<SocialInteractObject> $response */
        $response = $this->liveItem->getPodcastIndexSocialInteracts();
        $this->assertEquals($data, $response);
    }

    public function testSetSeason(): void
    {
        $season = [
            'value' => 3,
            'name'  => 'The Yearling - Chapter 3',
        ];
        $this->liveItem->setPodcastIndexSeason($season);
        $this->assertEquals($season, $this->liveItem->getPodcastIndexSeason());
    }

    public function testSetEpisode(): void
    {
        $episode = [
            'value'   => 3,
            'display' => 'Day 5',
        ];
        $this->liveItem->setPodcastIndexEpisode($episode);
        $this->assertEquals($episode, $this->liveItem->getPodcastIndexEpisode());
    }

    public function testSetAlternateEnclosure(): void
    {
        $sources            = [
            [
                'uri'         => 'https://example.com/file-720.torrent',
                'contentType' => 'application/x-bittorrent',
            ],
            [
                'uri' => 'ipfs://QmX33FYehk6ckGQ6g1D9D3FqZPix5JpKstKQKbaS8quUFb',
            ],
        ];
        $integrity          = [
            'type'  => 'sri',
            'value' => 'sha384-ExVqijgYHm15PqQqdXfW95x+Rs6C+d6E/ICxyQOeFevnxNLR/wtJNrNYTjIysUBo',
        ];
        $alternateEnclosure = [
            'type'    => 'video/mp4',
            'length'  => 7924786,
            'bitrate' => 511276.52,
            'height'  => 720,
            'lang'    => 'en',
            'title'   => 'Standard',
            'rel'     => 'default',
            'codecs'  => 'avc1.42E01E, mp4a.40.2',
            'default' => true,
        ];

        $this->liveItem->addPodcastIndexAlternateEnclosure($alternateEnclosure, $sources, $integrity);

        $alternateEnclosure['sources']   = $sources;
        $alternateEnclosure['integrity'] = $integrity;

        /** @psalm-var list<AlternateEnclosureObject> $actual */
        $actual = $this->liveItem->getPodcastIndexAlternateEnclosures();
        $this->assertEquals($alternateEnclosure, $actual[0]);
    }

    public function testSetContentLink(): void
    {
        $contentLinks = [
            [
                'href'        => 'https://youtube.com/pc20/livestream',
                'description' => 'YouTube!',
            ],
            [
                'href'        => 'https://twitch.com/pc20/livestream',
                'description' => 'Twitch!',
            ],
        ];

        $this->liveItem->setPodcastIndexContentLinks($contentLinks);

        /** @psalm-var list<ContentLinkObject> $actual */
        $actual = $this->liveItem->getPodcastIndexContentLinks();
        $this->assertEquals($contentLinks[0], $actual[0]);
        $this->assertEquals($contentLinks[1], $actual[1]);
    }

    public function testAddFunding(): void
    {
        $data = [
            'title' => 'Support the show!',
            'url'   => 'http://example.com/donate',
        ];
        $this->liveItem->addPodcastIndexFunding($data);

        /** @var list<array> $fundings */
        $fundings = $this->liveItem->getPodcastIndexFundings();
        $this->assertEquals($data, $fundings[0]);
    }

    public function testSetChat(): void
    {
        $data = [
            'server'    => "irc.zeronode.net",
            'protocol'  => "irc",
            'accountId' => "@jsmith",
            'space'     => "#myawesomepodcast",
        ];

        $this->liveItem->setPodcastIndexChat($data);
        $this->assertEquals($data, $this->liveItem->getPodcastIndexChat());
    }

    public function testSetPodcastIndexChatWithMinimalData(): void
    {
        $data = [
            'server'   => "irc.zeronode.net",
            'protocol' => "irc",
        ];

        $this->liveItem->setPodcastIndexChat($data);
        $this->assertEquals($data, $this->liveItem->getPodcastIndexChat());
    }
}
