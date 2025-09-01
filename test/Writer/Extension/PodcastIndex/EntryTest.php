<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Writer\Extension\PodcastIndex;

use Laminas\Feed\Reader\Extension\PodcastIndex\AttributesReader;
use Laminas\Feed\Writer;
use PHPUnit\Framework\TestCase;

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
 * @psalm-import-type ValueObject from AttributesReader
 * @psalm-import-type ImageObject from AttributesReader
 * @psalm-import-type SocialInteractObject from AttributesReader
 * @psalm-import-type TranscriptObject from AttributesReader
 * @psalm-import-type ChaptersObject from AttributesReader
 * @psalm-import-type SoundbiteObject from AttributesReader
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

    public function testSetLocation(): void
    {
        $entry = new Writer\Entry();

        $location = [
            'description' => 'London, Baker Street',
            'geo'         => 'geo:-27.86159,153.3169',
            'osm'         => 'W43678282',
            'rel'         => 'subject',
            'country'     => 'GB',
        ];
        $entry->setPodcastIndexLocation($location);
        $this->assertEquals($location, $entry->getPodcastIndexLocation());
    }

    public function testSetLocationWithOneArgument(): void
    {
        $entry = new Writer\Entry();

        $location = [
            'description' => 'London, Baker Street',
        ];
        $entry->setPodcastIndexLocation($location);
        $this->assertEquals($location, $entry->getPodcastIndexLocation());
    }

    public function testSetLocationThrowsExceptionOnInvalidArguments(): void
    {
        $entry = new Writer\Entry();

        $location = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->setPodcastIndexLocation($location);
    }

    public function testSetLocationThrowsExceptionOnInvalidGeo(): void
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
        $entry->setPodcastIndexLocation($location);
    }

    public function testSetLocationThrowsExceptionOnInvalidOsm(): void
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
        $entry->setPodcastIndexLocation($location);
    }

    public function testSetLocationThrowsExceptionOnInvalidRel(): void
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
        $entry->setPodcastIndexLocation($location);
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

    public function testAddValue(): void
    {
        $entry = new Writer\Entry();

        $value      = [
            'type'      => "lightning",
            'method'    => "keysend",
            'suggested' => 0.00000005000,
        ];
        $recipients = [
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
        $entry->addPodcastIndexValue($value, $recipients);
        $value['recipients'] = $recipients;

        /** @psalm-var list<ValueObject> $values */
        $values = $entry->getPodcastIndexValues();
        $this->assertContains($value, $values);
    }

    public function testAddValueWithMinimalArguments(): void
    {
        $entry = new Writer\Entry();

        $value      = [
            'type'   => "lightning",
            'method' => "keysend",
        ];
        $recipients = [
            [
                'type'    => "node",
                'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 40,
            ],
        ];
        $entry->addPodcastIndexValue($value, $recipients);
        $value['recipients'] = $recipients;

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

        $value      = [
            'type'   => "lightning",
            'method' => "keysend",
        ];
        $recipients = [
            [
                'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 40,
            ],
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexValue($value, $recipients);
    }

    public function testAddValueThrowsExceptionOnInvalidRecipientType(): void
    {
        $entry = new Writer\Entry();

        $value      = [
            'type'   => "lightning",
            'method' => "keysend",
        ];
        $recipients = [
            [
                'type'    => true,
                'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 40,
            ],
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexValue($value, $recipients);
    }

    public function testAddValueUsingScientificNotation(): void
    {
        $entry = new Writer\Entry();

        $value      = [
            'type'      => "lightning",
            'method'    => "keysend",
            'suggested' => 5.0E-8, // scientific notation for 0.00000005000
        ];
        $recipients = [
            [
                'name'    => "Alice (Podcaster)",
                'type'    => "node",
                'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 40,
            ],
        ];
        $entry->addPodcastIndexValue($value, $recipients);

        $value['recipients'] = $recipients;

        /** @psalm-var list<ValueObject> $values */
        $values = $entry->getPodcastIndexValues();
        $this->assertContains($value, $values);
    }

    public function testResetValues(): void
    {
        $entry = new Writer\Entry();

        $value      = [
            'type'   => "lightning",
            'method' => "keysend",
        ];
        $recipients = [
            [
                'type'    => "node",
                'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 40,
            ],
        ];

        // set values
        $entry->addPodcastIndexValue($value, $recipients);
        $value['recipients'] = $recipients;

        /** @psalm-var list<ValueObject> $values */
        $values = $entry->getPodcastIndexValues();
        $this->assertContains($value, $values);

        // remove them again
        $entry->resetPodcastIndexValues();

        /** @psalm-var list<ValueObject> $empty */
        $empty = $entry->getPodcastIndexValues();
        $this->assertEmpty($empty);
    }
}
