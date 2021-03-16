<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Writer\Extension\PodcastIndex;

use Laminas\Feed\Writer;
use PHPUnit\Framework\TestCase;

class EntryTest extends TestCase
{
    public function testSetTranscript(): void
    {
        $entry = new Writer\Entry();

        $transcript = [
            'url' => 'https://example.com/podcasts/everything/TranscriptEpisode3.html',
            'type' => 'text/html',
        ];
        $entry->setPodcastIndexTranscript($transcript);
        $this->assertEquals($transcript, $entry->getPodcastIndexTranscript());
    }

    public function testSetTranscriptWithOptionalArguments(): void
    {
        $entry = new Writer\Entry();

        $transcript = [
            'url' => 'https://example.com/podcasts/everything/TranscriptEpisode3.html',
            'type' => 'text/html',
            'language' => 'en',
            'rel' => 'captions',
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
            'url' => 'https://example.com/podcasts/everything/ChaptersEpisode3.json',
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
    public function invalidTimeValues(): array
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

    public function testAddSoundbites(): void
    {
        $entry = new Writer\Entry();

        $soundbites = [
            [
                'startTime' => '66',
                'duration' => '39.0',
                'title' => 'Pepper shakers comparison',
            ],
            [
                'startTime' => '112.45',
                'duration' => '24.83',
                'title' => 'Pepper shakers comparison',
            ]
        ];

        $entry->addPodcastIndexSoundbites($soundbites);
        $this->assertEquals($soundbites, $entry->getPodcastIndexSoundbites());
    }

    public function testAddSoundbitesThrowsExceptionOnInvalidArguments(): void
    {
        $entry = new Writer\Entry();

        $soundbites = [
            [
                'title' => 'Pepper shakers comparison',
                'abc' => 'def',
            ]
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexSoundbites($soundbites);
    }

    /**
     * @dataProvider invalidTimeValues
     *
     * @param mixed $time
     */
    public function testAddSoundbitesThrowsExceptionOnNonNumericStartTimeValue($time): void
    {
        $entry = new Writer\Entry();

        $soundbites = [
            [
                'startTime' => $time,
                'duration' => '39.0',
                'title' => 'Pepper shakers comparison',
            ],
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexSoundbites($soundbites);
    }

    /**
     * @dataProvider invalidTimeValues
     *
     * @param mixed $time
     */
    public function testAddSoundbitesThrowsExceptionOnNonNumericDurationValue($time): void
    {
        $entry = new Writer\Entry();

        $soundbites = [
            [
                'startTime' => '66',
                'duration' => $time,
                'title' => 'Pepper shakers comparison',
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
                'duration' => '39.0',
                'title' => 'Pepper shakers comparison',
            ],
            [
                'startTime' => '112.45',
                'duration' => '24.83',
                'title' => 'Pepper shakers comparison',
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
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexSoundbite($soundbite);
    }

    /**
     * @dataProvider invalidTimeValues
     *
     * @param mixed $time
     */
    public function testAddSoundbiteThrowsExceptionOnNonNumericStartTimeValue($time): void
    {
        $entry = new Writer\Entry();

        $soundbite = [
            'startTime' => $time,
            'duration' => '39.0',
            'title' => 'Pepper shakers comparison',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexSoundbite($soundbite);
    }

    /**
     * @dataProvider invalidTimeValues
     *
     * @param mixed $time
     */
    public function testAddSoundbiteThrowsExceptionOnNonNumericDurationValue($time): void
    {
        $entry = new Writer\Entry();

        $soundbite = [
            'startTime' => '66',
            'duration' => $time,
            'title' => 'Pepper shakers comparison',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $entry->addPodcastIndexSoundbite($soundbite);
    }
}
