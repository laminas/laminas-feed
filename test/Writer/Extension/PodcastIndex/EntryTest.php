<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Writer\Extension\PodcastIndex;

use Laminas\Feed\Reader\Extension\PodcastIndex\AttributesReader;
use Laminas\Feed\Writer;
use PHPUnit\Framework\TestCase;

use function array_diff_key;
use function array_key_first;
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
class EntryTest extends TestCase
{
    public function testSetTranscript(): void
    {
        $entry = new Writer\Entry();

        $transcript = [
            'url'  => 'https://example.com/podcasts/everything/TranscriptEpisode3.html',
            'type' => 'text/html',
        ];
        $entry->setPodcastIndexTranscript($transcript);
        $this->assertEquals($transcript, $entry->getPodcastIndexTranscript());
    }

    public function testSetTranscriptWithOptionalArguments(): void
    {
        $entry = new Writer\Entry();

        $transcript = [
            'url'      => 'https://example.com/podcasts/everything/TranscriptEpisode3.html',
            'type'     => 'text/html',
            'language' => 'en',
            'rel'      => 'captions',
        ];
        $entry->setPodcastIndexTranscript($transcript);
        $this->assertEquals($transcript, $entry->getPodcastIndexTranscript());
    }

    public function testSetTranscriptThrowsExceptionOnInvalidArguments(): void
    {
        $entry = new Writer\Entry();

        $transcript = [
            'url' => 'https://example.com/podcasts/everything/TranscriptEpisode3.html',
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->setPodcastIndexTranscript($transcript);
    }

    public function testSetChapters(): void
    {
        $entry = new Writer\Entry();

        $chapters = [
            'url'  => 'https://example.com/podcasts/everything/ChaptersEpisode3.json',
            'type' => 'application/json+chapters',
        ];
        $entry->setPodcastIndexChapters($chapters);
        $this->assertEquals($chapters, $entry->getPodcastIndexChapters());
    }

    public function testSetChaptersThrowsExceptionOnInvalidArguments(): void
    {
        $entry = new Writer\Entry();

        $chapters = [
            'url' => 'https://example.com/podcasts/everything/ChaptersEpisode3.json',
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->setPodcastIndexChapters($chapters);
    }

    /**
     * @psalm-return array<string, array{0: mixed}>
     */
    public static function invalidTimeValues(): array
    {
        return [
            'null'       => [null],
            'zero'       => [0],
            'int'        => [1],
            'zero-float' => [0.0],
            'float'      => [1.1],
            'array'      => [['1.1']],
            'object'     => [(object) ['time' => '1.1']],
        ];
    }

    public function testAddSoundbitesAndSetSoundbites(): void
    {
        $entry = new Writer\Entry();

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

        $entry->addPodcastIndexSoundbites($soundbites);
        $this->assertEquals($soundbites, $entry->getPodcastIndexSoundbites());

        // set new
        $entry->setPodcastIndexSoundbites($soundbites);
        $this->assertEquals($soundbites, $entry->getPodcastIndexSoundbites());

        // remove
        $entry->setPodcastIndexSoundbites();
        $this->assertNull($entry->getPodcastIndexSoundbites());
    }

    public function testAddSoundbitesThrowsExceptionOnInvalidArguments(): void
    {
        $entry = new Writer\Entry();

        $soundbites = [
            [
                'title' => 'Pepper shakers comparison',
                'abc'   => 'def',
            ],
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexSoundbites($soundbites);
    }

    /**
     * @dataProvider invalidTimeValues
     * @param mixed $time
     */
    public function testAddSoundbitesThrowsExceptionOnNonNumericStartTimeValue($time): void
    {
        $entry = new Writer\Entry();

        $soundbites = [
            [
                'startTime' => $time,
                'duration'  => '39.0',
                'title'     => 'Pepper shakers comparison',
            ],
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexSoundbites($soundbites);
    }

    /**
     * @dataProvider invalidTimeValues
     * @param mixed $time
     */
    public function testAddSoundbitesThrowsExceptionOnNonNumericDurationValue($time): void
    {
        $entry = new Writer\Entry();

        $soundbites = [
            [
                'startTime' => '66',
                'duration'  => $time,
                'title'     => 'Pepper shakers comparison',
            ],
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexSoundbites($soundbites);
    }

    public function testAddSoundbite(): void
    {
        $entry = new Writer\Entry();

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
            $entry->addPodcastIndexSoundbite($soundbite);
        }
        $this->assertEquals($soundbites, $entry->getPodcastIndexSoundbites());
    }

    public function testAddSoundbiteThrowsExceptionOnInvalidArguments(): void
    {
        $entry = new Writer\Entry();

        $soundbite = [
            'title' => 'Pepper shakers comparison',
            'abc'   => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexSoundbite($soundbite);
    }

    /**
     * @dataProvider invalidTimeValues
     * @param mixed $time
     */
    public function testAddSoundbiteThrowsExceptionOnNonNumericStartTimeValue($time): void
    {
        $entry = new Writer\Entry();

        $soundbite = [
            'startTime' => $time,
            'duration'  => '39.0',
            'title'     => 'Pepper shakers comparison',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexSoundbite($soundbite);
    }

    /**
     * @dataProvider invalidTimeValues
     * @param mixed $time
     */
    public function testAddSoundbiteThrowsExceptionOnNonNumericDurationValue($time): void
    {
        $entry = new Writer\Entry();

        $soundbite = [
            'startTime' => '66',
            'duration'  => $time,
            'title'     => 'Pepper shakers comparison',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexSoundbite($soundbite);
    }

    public function testAddLocation(): void
    {
        $entry = new Writer\Entry();

        $location = [
            'description' => 'London, Baker Street',
            'geo'         => 'geo:-27.86159,153.3169',
            'osm'         => 'W43678282',
            'rel'         => 'subject',
            'country'     => 'GB',
        ];
        $entry->addPodcastIndexLocation($location);

        /** @var list<LocationObject> $locations */
        $locations = $entry->getPodcastIndexLocations();
        $this->assertTrue(in_array($location, $locations));
    }

    public function testSetLocations(): void
    {
        $entry = new Writer\Entry();

        $location = [
            [
                'description' => 'London, Baker Street',
                'geo'         => 'geo:-27.86159,153.3169',
                'osm'         => 'W43678282',
                'rel'         => 'creator',
                'country'     => 'GB',
            ],
            [
                'description' => 'Marlow',
                'geo'         => 'geo:51.5718706,-0.7769654',
                'osm'         => 'R3727240',
                'rel'         => 'subject',
                'country'     => 'US',
            ],
        ];
        $entry->setPodcastIndexLocations($location);
        $this->assertEquals($location, $entry->getPodcastIndexLocations());
    }

    public function testAddLocationWithOneArgument(): void
    {
        $entry = new Writer\Entry();

        $location = [
            'description' => 'London, Baker Street',
        ];
        $entry->addPodcastIndexLocation($location);

        /** @var list<LocationObject> $locations */
        $locations = $entry->getPodcastIndexLocations();
        $this->assertTrue(in_array($location, $locations));
    }

    public function testAddLocationThrowsExceptionOnInvalidArguments(): void
    {
        $entry = new Writer\Entry();

        $location = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexLocation($location);
    }

    public function testAddLocationThrowsExceptionOnInvalidGeo(): void
    {
        $entry = new Writer\Entry();

        $location = [
            'description' => 'London, Baker Street',
            'geo'         => [-27.86159, 153.3169],
            'osm'         => 'W43678282',
            'rel'         => 'subject',
            'country'     => 'GB',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexLocation($location);
    }

    public function testAddLocationThrowsExceptionOnInvalidOsm(): void
    {
        $entry = new Writer\Entry();

        $location = [
            'description' => 'London, Baker Street',
            'geo'         => 'geo:-27.86159,153.3169',
            'osm'         => false,
            'rel'         => 'subject',
            'country'     => 'GB',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexLocation($location);
    }

    public function testAddLocationThrowsExceptionOnInvalidRel(): void
    {
        $entry = new Writer\Entry();

        $location = [
            'description' => 'London, Baker Street',
            'geo'         => 'geo:-27.86159,153.3169',
            'osm'         => 'W43678282',
            'rel'         => 1234,
            'country'     => 'GB',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexLocation($location);
    }

    public function testAddPerson(): void
    {
        $entry = new Writer\Entry();

        $person = [
            'name'  => 'Hercules Poirot',
            'role'  => 'guest',
            'group' => 'starring',
            'img'   => 'https://poirot.com/about/my-moustage.jpg',
            'href'  => 'https://poirot.com/my-cases',
        ];
        $entry->addPodcastIndexPerson($person);

        /** @var list<PersonObject> $people */
        $people = $entry->getPodcastIndexPeople();
        $this->assertTrue(in_array($person, $people));
    }

    public function testSetPeopleAndSetPersons(): void
    {
        $entry = new Writer\Entry();

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
        $entry->setPodcastIndexPeople($people);
        /** @var list<PersonObject> $peopleSaved */
        $peopleSaved = $entry->getPodcastIndexPeople();
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
        $entry->setPodcastIndexPersons($newPersons);
        /** @var list<PersonObject> $updated */
        $updated = $entry->getPodcastIndexPersons();
        $this->assertEquals(1, count($updated));
        $this->assertEquals($newPersons, $updated);

        // delete using "people"
        $entry->setPodcastIndexPeople();
        $this->assertNull($entry->getPodcastIndexPeople());
    }

    public function testAddPersonWithOneArgument(): void
    {
        $entry = new Writer\Entry();

        $person = [
            'name' => 'Hercules Poirot',
        ];
        $entry->addPodcastIndexPerson($person);

        /** @var list<PersonObject> $people */
        $people = $entry->getPodcastIndexPeople();
        $this->assertTrue(in_array($person, $people));
    }

    public function testAddPersonThrowsExceptionOnInvalidArguments(): void
    {
        $entry = new Writer\Entry();

        $person = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexPerson($person);
    }

    public function testAddPersonThrowsExceptionOnInvalidImageUrl(): void
    {
        $entry = new Writer\Entry();

        $person = [
            'name'  => 'Hercules Poirot',
            'role'  => 'guest',
            'group' => 'writing',
            'img'   => 'poirot.com/my-moustage.jpg',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexPerson($person);
    }

    public function testAddTxt(): void
    {
        $entry = new Writer\Entry();

        $txt = [
            'value'   => 'S6lpp-7ZCn8-dZfGc-OoyaG',
            'purpose' => 'verify',
        ];
        $entry->addPodcastIndexTxt($txt);

        /** @var list<array{value: string, purpose?: string}> $txts */
        $txts = $entry->getPodcastIndexTxts();
        $this->assertTrue(in_array($txt, $txts));
    }

    public function testSetTxts(): void
    {
        $entry = new Writer\Entry();

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
        $entry->setPodcastIndexTxts($txts);
        /** @var list<object{value: string, purpose?: string}> $txtsSaved */
        $txtsSaved = $entry->getPodcastIndexTxts();
        foreach ($txts as $txt) {
            $this->assertTrue(in_array($txt, $txtsSaved));
        }

        // delete
        $entry->setPodcastIndexTxts();
        $this->assertNull($entry->getPodcastIndexTxts());
    }

    public function testAddTxtWithOneArgument(): void
    {
        $entry = new Writer\Entry();

        $txt = [
            'value' => 'naj3eEZaWVVY9a38uhX8FekACyhtqP4JN',
        ];
        $entry->addPodcastIndexTxt($txt);

        /** @psalm-var list<object{value: string, purpose?: string}> $txts */
        $txts = $entry->getPodcastIndexTxts();
        $this->assertTrue(in_array($txt, $txts));
    }

    public function testAddTxtThrowsExceptionOnInvalidArguments(): void
    {
        $entry = new Writer\Entry();

        $data = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexTxt($data);
    }

    public function testAddTxtThrowsExceptionOnInvalidValue(): void
    {
        $entry = new Writer\Entry();

        $data = [
            'value'   => true,
            'purpose' => 'google',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexTxt($data);
    }

    public function testSetSocialInteracts(): void
    {
        $entry = new Writer\Entry();

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
        $entry->setPodcastIndexSocialInteracts($data);

        /** @psalm-var list<SocialInteractObject> $response */
        $response = $entry->getPodcastIndexSocialInteracts();
        $this->assertEquals($data, $response);
    }

    public function testAddSocialInteractThrowsExceptionOnInvalidUri(): void
    {
        $entry = new Writer\Entry();

        $data = [
            [
                'priority'   => 1,
                'protocol'   => "activitypub",
                'uri'        => "podcastindex.social/web/@dave/108013847520053258",
                'accountId'  => "@dave",
                'accountUrl' => "https://podcastindex.social/web/@dave",
            ],
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexSocialInteract($data);
    }

    public function testAddSocialInteractThrowsExceptionOnMissingProtocol(): void
    {
        $entry = new Writer\Entry();

        $data = [
            [
                'priority'  => 1,
                'uri'       => "https://podcastindex.social/web/@dave",
                'accountId' => "@dave",
            ],
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexSocialInteract($data);
    }

    public function testAddValueWithTimeSplitRecipients(): void
    {
        $entry = new Writer\Entry();

        $value           = [
            'type'      => "lightning",
            'method'    => "keysend",
            'suggested' => 0.00000005000,
        ];
        $valueRecipients = [
            [
                'name'    => "Alice (Podcaster)",
                'type'    => "node",
                'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 40,
            ],
            [
                'name'    => "Bob (Podcaster)",
                'type'    => "node",
                'address' => "032f4ffbbafffbe51726ad3c164a3d0d37ec27bc67b29a159b0f49ae8ac21b8508",
                'split'   => 60,
            ],
        ];
        $valueTimeSplits = [
            [
                'startTime'       => 63,
                'duration'        => 388,
                'valueRecipients' => [
                    [
                        'name'        => "Alice (Podcaster)",
                        'type'        => "node",
                        'address'     => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                        'split'       => 40,
                        'customKey'   => "Some_custom_key",
                        'customValue' => "Some_custom_value",
                        'fee'         => true,
                    ],
                    [
                        'name'    => "Malcolm (Guest)",
                        'type'    => "node",
                        'address' => "02dd306e68c46681aa21d88a436fb35355a8579dd30201581cefa17cb179fc4c15",
                        'split'   => 20,
                        'fee'     => true,
                    ],
                ],
            ],
        ];
        $entry->addPodcastIndexValue($value, $valueRecipients, $valueTimeSplits);
        $value['valueRecipients'] = $valueRecipients;
        $value['valueTimeSplits'] = $valueTimeSplits;

        /** @psalm-var list<ValueObject> $values */
        $values = $entry->getPodcastIndexValues();

        /* print_r($values);
        die(); */


        $this->assertContains($value, $values);
    }

    public function testAddValueWithTimeSplitRemoteItems(): void
    {
        $entry = new Writer\Entry();

        $value           = [
            'type'   => "lightning",
            'method' => "keysend",
        ];
        $valueRecipients = [
            [
                'type'    => "node",
                'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 40,
            ],
        ];
        $valueTimeSplits = [
            [
                'startTime'  => 82,
                'duration'   => 200,
                'remoteItem' => [
                    'itemGuid' => "https://podcastindex.org/podcast/4148683#1",
                    'feedGuid' => "a94f5cc9-8c58-55fc-91fe-a324087a655b",
                    'medium'   => "music",
                ],
            ],
            [
                'startTime'  => 134,
                'duration'   => 123,
                'remoteItem' => [
                    'itemGuid' => "https://podcastindex.org/podcast/4148683#3",
                    'feedGuid' => "b83f5cc9-8c58-55fc-91fe-a324087a644c",
                    'medium'   => "podcast",
                    'feedUrl'  => "https://podcastindex.org/podcast/4148683",
                    'title'    => "My Fancy Podcast",
                ],
            ],
        ];
        $entry->addPodcastIndexValue($value, $valueRecipients, $valueTimeSplits);
        $value['valueRecipients'] = $valueRecipients;
        $value['valueTimeSplits'] = $valueTimeSplits;

        /** @psalm-var list<ValueObject> $values */
        $values = $entry->getPodcastIndexValues();

        $this->assertContains($value, $values);
    }

    public function testAddValueWithMinimalArguments(): void
    {
        $entry = new Writer\Entry();

        $value           = [
            'type'   => "lightning",
            'method' => "keysend",
        ];
        $valueRecipients = [
            [
                'type'    => "node",
                'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 40,
            ],
        ];
        $entry->addPodcastIndexValue($value, $valueRecipients);
        $value['valueRecipients'] = $valueRecipients;

        /** @psalm-var list<ValueObject> $values */
        $values = $entry->getPodcastIndexValues();
        $this->assertContains($value, $values);
    }

    public function testAddValueThrowsExceptionOnMissingRecipients(): void
    {
        $entry = new Writer\Entry();

        $value = [
            'type'   => "lightning",
            'method' => "keysend",
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexValue($value, []);
    }

    public function testAddValueThrowsExceptionOnMissingRecipientType(): void
    {
        $entry = new Writer\Entry();

        $value           = [
            'type'   => "lightning",
            'method' => "keysend",
        ];
        $valueRecipients = [
            [
                'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 40,
            ],
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexValue($value, $valueRecipients);
    }

    public function testAddValueThrowsExceptionOnInvalidRecipientType(): void
    {
        $entry = new Writer\Entry();

        $value           = [
            'type'   => "lightning",
            'method' => "keysend",
        ];
        $valueRecipients = [
            [
                'type'    => true,
                'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 40,
            ],
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexValue($value, $valueRecipients);
    }

    public function testAddValueUsingScientificNotation(): void
    {
        $entry = new Writer\Entry();

        $value           = [
            'type'      => "lightning",
            'method'    => "keysend",
            'suggested' => 5.0E-8, // scientific notation for 0.00000005000
        ];
        $valueRecipients = [
            [
                'name'    => "Alice (Podcaster)",
                'type'    => "node",
                'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 40,
            ],
        ];
        $entry->addPodcastIndexValue($value, $valueRecipients);

        $value['valueRecipients'] = $valueRecipients;

        /** @psalm-var list<ValueObject> $values */
        $values = $entry->getPodcastIndexValues();
        $this->assertContains($value, $values);
    }

    public function testResetValues(): void
    {
        $entry = new Writer\Entry();

        $value           = [
            'type'   => "lightning",
            'method' => "keysend",
        ];
        $valueRecipients = [
            [
                'type'    => "node",
                'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 40,
            ],
        ];

        // set values
        $entry->addPodcastIndexValue($value, $valueRecipients);
        $value['valueRecipients'] = $valueRecipients;

        /** @psalm-var list<ValueObject> $values */
        $values = $entry->getPodcastIndexValues();
        $this->assertContains($value, $values);

        // remove them again
        $entry->resetPodcastIndexValues();

        /** @psalm-var list<ValueObject> $empty */
        $empty = $entry->getPodcastIndexValues();
        $this->assertEmpty($empty);
    }

    public function testSetSeason(): void
    {
        $entry = new Writer\Entry();

        $season = [
            'value' => 3,
            'name'  => 'The Yearling - Chapter 3',
        ];
        $entry->setPodcastIndexSeason($season);
        $this->assertEquals($season, $entry->getPodcastIndexSeason());
    }

    public function testSetSeasonThrowsExceptionOnInvalidArgument(): void
    {
        $entry = new Writer\Entry();

        $season = [
            'something' => 123,
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->setPodcastIndexSeason($season);
    }

    public function testSetSeasonThrowsExceptionOnInvalidValue(): void
    {
        $entry = new Writer\Entry();

        $season = [
            'value' => '3',
            'name'  => 'The Yearling - Chapter 3',
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->setPodcastIndexSeason($season);
    }

    public function testSetSeasonThrowsExceptionOnInvalidName(): void
    {
        $entry = new Writer\Entry();

        $season = [
            'value' => 3,
            'name'  => 123,
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->setPodcastIndexSeason($season);
    }

    public function testSetEpisode(): void
    {
        $entry = new Writer\Entry();

        $episode = [
            'value'   => 3,
            'display' => 'Day 5',
        ];
        $entry->setPodcastIndexEpisode($episode);
        $this->assertEquals($episode, $entry->getPodcastIndexEpisode());
    }

    public function testSetEpisodeUsingDecimal(): void
    {
        $entry = new Writer\Entry();

        $episode = [
            'value' => 3.5,
        ];
        $entry->setPodcastIndexEpisode($episode);
        $this->assertEquals($episode, $entry->getPodcastIndexEpisode());
    }

    public function testSetEpisodeThrowsExceptionOnInvalidArgument(): void
    {
        $entry = new Writer\Entry();

        $episode = [
            'something' => 123,
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->setPodcastIndexEpisode($episode);
    }

    public function testSetEpisodeThrowsExceptionOnInvalidValue(): void
    {
        $entry = new Writer\Entry();

        $episode = [
            'value'   => '3',
            'display' => 'Day 5',
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->setPodcastIndexEpisode($episode);
    }

    public function testSetEpisodeThrowsExceptionOnInvalidDisplay(): void
    {
        $entry = new Writer\Entry();

        $episode = [
            'value'   => 3,
            'display' => 123,
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->setPodcastIndexEpisode($episode);
    }

    public function testSetAlternateEnclosure(): void
    {
        $entry = new Writer\Entry();

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

        $entry->addPodcastIndexAlternateEnclosure($alternateEnclosure, $sources, $integrity);

        $alternateEnclosure['sources']   = $sources;
        $alternateEnclosure['integrity'] = $integrity;

        /** @psalm-var list<AlternateEnclosureObject> $actual */
        $actual = $entry->getPodcastIndexAlternateEnclosures();
        $this->assertEquals($alternateEnclosure, $actual[0]);
    }

    public function testSetAlternateEnclosureWithMinimalAttributes(): void
    {
        $entry              = new Writer\Entry();
        $sources            = [
            ['uri' => 'ipfs://QmX33FYehk6ckGQ6g1D9D3FqZPix5JpKstKQKbaS8quUFb'],
        ];
        $alternateEnclosure = [
            'type' => 'video/mp4',
        ];

        $entry->addPodcastIndexAlternateEnclosure($alternateEnclosure, $sources);

        $alternateEnclosure['sources'] = $sources;

        /** @psalm-var list<AlternateEnclosureObject> $actual */
        $actual = $entry->getPodcastIndexAlternateEnclosures();
        $this->assertEquals($alternateEnclosure, $actual[0]);
    }

    public function testSetAlternateEnclosureThrowsExceptionOnInvalidArgument(): void
    {
        $entry   = new Writer\Entry();
        $sources = [
            ['uri' => 'ipfs://QmX33FYehk6ckGQ6g1D9D3FqZPix5JpKstKQKbaS8quUFb'],
        ];

        $invalidEnclosures = [
            [
                'something_wrong' => 1234,
            ],
            [
                'type'   => 'video/mp4',
                'length' => '7924786 seconds',
            ],
            [
                'type'    => 'video/mp4',
                'bitrate' => '511276.52',
            ],
            [
                'type'   => 'video/mp4',
                'height' => '720px',
            ],
            [
                'type' => 'video/mp4',
                'lang' => 1234,
            ],
            [
                'type'  => 'video/mp4',
                'title' => false,
            ],
            [
                'type' => 'video/mp4',
                'rel'  => true,
            ],
            [
                'type'   => 'video/mp4',
                'codecs' => true,
            ],
            [
                'type'    => 'video/mp4',
                'default' => 'yes',
            ],
        ];

        foreach ($invalidEnclosures as $alternateEnclosure) {
            try {
                $entry->addPodcastIndexAlternateEnclosure($alternateEnclosure, $sources);
                $invalidKey = array_key_first(array_diff_key($alternateEnclosure, ['type' => true]));
                $this->assertTrue(false, "Expected exception was not thrown on the invalid key `$invalidKey`");
            } catch (Writer\Exception\InvalidArgumentException $e) {
                $this->assertTrue(true);
            }
        }
    }

    public function testSetAlternateEnclosureThrowsExceptionOnMissingSources(): void
    {
        $entry              = new Writer\Entry();
        $sources            = [];
        $alternateEnclosure = [
            'type' => 'video/mp4',
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexAlternateEnclosure($alternateEnclosure, $sources);
    }

    public function testSetAlternateEnclosureThrowsExceptionOnInvalidSourceUri(): void
    {
        $entry              = new Writer\Entry();
        $sources            = [
            ['uri' => 1234],
        ];
        $alternateEnclosure = [
            'type' => 'video/mp4',
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexAlternateEnclosure($alternateEnclosure, $sources);
    }

    public function testSetDetailedImages(): void
    {
        $entry  = new Writer\Entry();
        $images = [
            [
                'alt'         => "An antenna emanating signal waves",
                'purpose'     => "artwork",
                'type'        => "image/jpeg",
                'aspectRatio' => "1/1",
                'href'        => "https://example.com/images/ep1/pci_square-massive.jpg",
                'width'       => 1400,
                'height'      => 1400,
            ],
            [
                'alt'         => "Another antenna emanating signal waves",
                'purpose'     => "artwork social",
                'type'        => "image/jpeg",
                'aspectRatio' => "16/9",
                'href'        => "https://example.com/images/ep1/pci_landscape-massive_wide.jpg",
            ],
        ];

        $entry->setPodcastIndexDetailedImages($images);
        $this->assertEquals($images, $entry->getPodcastIndexDetailedImages());
    }

    public function testSetContentLinks(): void
    {
        $entry = new Writer\Entry();

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

        $entry->setPodcastIndexContentLinks($contentLinks);

        /** @psalm-var list<ContentLinkObject> $actual */
        $actual = $entry->getPodcastIndexContentLinks();
        $this->assertEquals($contentLinks[0], $actual[0]);
        $this->assertEquals($contentLinks[1], $actual[1]);
    }

    public function testAddContentLinkThrowsExceptionOnMissingDescription(): void
    {
        $entry = new Writer\Entry();

        $contentLink = [
            'href' => 'https://youtube.com/pc20/livestream',
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexContentLink($contentLink);
    }

    public function testAddContentLinkThrowsExceptionOnInvalidHref(): void
    {
        $entry = new Writer\Entry();

        $contentLink = [
            'href'        => 'youtube.com/pc20/livestream',
            'description' => 'YouTube!',
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexContentLink($contentLink);
    }

    public function testAddContentLinkThrowsExceptionOnInvalidDescription(): void
    {
        $entry = new Writer\Entry();

        $contentLink = [
            'href'        => 'https://youtube.com/pc20/livestream',
            'description' => true,
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexContentLink($contentLink);
    }

    public function testAddFunding(): void
    {
        $entry = new Writer\Entry();

        $funding = [
            'title' => 'Support the show!',
            'url'   => 'http://example.com/donate',
        ];
        $entry->addPodcastIndexFunding($funding);
        /** @var array $fundings */
        $fundings = $entry->getPodcastIndexFundings();
        $this->assertEquals($funding, $fundings[0]);
    }

    public function testAddFundingThrowsExceptionOnInvalidArguments(): void
    {
        $entry = new Writer\Entry();

        $funding = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexFunding($funding);
    }

    public function testAddFundingThrowsExceptionOnInvalidUrl(): void
    {
        $entry = new Writer\Entry();

        $funding = [
            'title' => 'Support the show!',
            'url'   => 'example.com/donate',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexFunding($funding);
    }

    public function testSetFundings(): void
    {
        $entry = new Writer\Entry();

        $fundings = [
            [
                'title' => 'Support the show!',
                'url'   => 'http://example.com/donate',
            ],
            [
                'title' => 'Buy me a coffee',
                'url'   => 'http://example.com/coffee',
            ],
        ];
        $entry->setPodcastIndexFundings($fundings);
        $this->assertEquals($fundings, $entry->getPodcastIndexFundings());
    }

    public function testSetChat(): void
    {
        $entry = new Writer\Entry();

        $data = [
            'server'    => "irc.zeronode.net",
            'protocol'  => "irc",
            'accountId' => "@jsmith",
            'space'     => "#myawesomepodcast",
        ];

        $entry->setPodcastIndexChat($data);
        $this->assertEquals($data, $entry->getPodcastIndexChat());
    }

    public function testSetPodcastIndexChatWithMinimalData(): void
    {
        $entry = new Writer\Entry();

        $data = [
            'server'   => "irc.zeronode.net",
            'protocol' => "irc",
        ];

        $entry->setPodcastIndexChat($data);
        $this->assertEquals($data, $entry->getPodcastIndexChat());
    }

    public function testSetPodcastIndexChatThrowsExceptionOnInvalidArgument(): void
    {
        $entry = new Writer\Entry();

        $data = [
            'abc' => 123,
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->setPodcastIndexChat($data);
    }

    public function testSetPodcastIndexChatThrowsExceptionOnInvalidServer(): void
    {
        $entry = new Writer\Entry();

        $data = [
            'server'    => 123,
            'protocol'  => "irc",
            'accountId' => "@jsmith",
            'space'     => "#myawesomepodcast",
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->setPodcastIndexChat($data);
    }

    public function testSetPodcastIndexChatThrowsExceptionOnInvalidSpace(): void
    {
        $entry = new Writer\Entry();

        $data = [
            'server'    => 'server_name',
            'protocol'  => "irc",
            'accountId' => "@jsmith",
            'space'     => true,
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->setPodcastIndexChat($data);
    }
}
