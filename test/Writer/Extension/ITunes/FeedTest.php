<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Writer\Extension\ITunes;

use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Exception\ExceptionInterface;
use PHPUnit\Framework\TestCase;

use function preg_match;
use function restore_error_handler;
use function set_error_handler;
use function str_repeat;

use const E_USER_DEPRECATED;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Writer
 */
class FeedTest extends TestCase
{
    public function testSetBlock(): void
    {
        $feed = new Writer\Feed();
        $feed->setItunesBlock('yes');
        $this->assertEquals('yes', $feed->getItunesBlock());
    }

    public function testSetBlockThrowsExceptionOnNonAlphaValue(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setItunesBlock('123');
    }

    public function testSetBlockThrowsExceptionIfValueGreaterThan255CharsLength(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setItunesBlock(str_repeat('a', 256));
    }

    public function testAddAuthors(): void
    {
        $feed = new Writer\Feed();
        $feed->addItunesAuthors(['joe', 'jane']);
        $this->assertEquals(['joe', 'jane'], $feed->getItunesAuthors());
    }

    public function testAddAuthor(): void
    {
        $feed = new Writer\Feed();
        $feed->addItunesAuthor('joe');
        $this->assertEquals(['joe'], $feed->getItunesAuthors());
    }

    public function testAddAuthorThrowsExceptionIfValueGreaterThan255CharsLength(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->addItunesAuthor(str_repeat('a', 256));
    }

    public function testSetCategories(): void
    {
        $feed = new Writer\Feed();
        $cats = [
            'cat1',
            'cat2' => ['cat2-1', 'cat2-a&b'],
        ];
        $feed->setItunesCategories($cats);
        $this->assertEquals($cats, $feed->getItunesCategories());
    }

    public function testSetCategoriesThrowsExceptionIfAnyCatNameGreaterThan255CharsLength(): void
    {
        $feed = new Writer\Feed();
        $cats = [
            'cat1',
            'cat2' => ['cat2-1', str_repeat('a', 256)],
        ];

        $this->expectException(ExceptionInterface::class);
        $feed->setItunesCategories($cats);
    }

    public function testSetImageAsPngFile(): void
    {
        $feed = new Writer\Feed();
        $feed->setItunesImage('http://www.example.com/image.png');
        $this->assertEquals('http://www.example.com/image.png', $feed->getItunesImage());
    }

    public function testSetImageAsJpgFile(): void
    {
        $feed = new Writer\Feed();
        $feed->setItunesImage('http://www.example.com/image.jpg');
        $this->assertEquals('http://www.example.com/image.jpg', $feed->getItunesImage());
    }

    public function testSetImageThrowsExceptionOnInvalidUri(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setItunesImage('http://');
    }

    public function testSetImageThrowsExceptionOnInvalidImageExtension(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setItunesImage('http://www.example.com/image.gif');
    }

    public function testSetDurationAsSeconds(): void
    {
        $feed = new Writer\Feed();
        $feed->setItunesDuration(23);
        $this->assertEquals(23, $feed->getItunesDuration());
    }

    public function testSetDurationAsMinutesAndSeconds(): void
    {
        $feed = new Writer\Feed();
        $feed->setItunesDuration('23:23');
        $this->assertEquals('23:23', $feed->getItunesDuration());
    }

    public function testSetDurationAsHoursMinutesAndSeconds(): void
    {
        $feed = new Writer\Feed();
        $feed->setItunesDuration('23:23:23');
        $this->assertEquals('23:23:23', $feed->getItunesDuration());
    }

    public function testSetDurationThrowsExceptionOnUnknownFormat(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setItunesDuration('abc');
    }

    public function testSetDurationThrowsExceptionOnInvalidSeconds(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setItunesDuration('23:456');
    }

    public function testSetDurationThrowsExceptionOnInvalidMinutes(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setItunesDuration('23:234:45');
    }

    /**
     * @dataProvider dataProviderForSetExplicit
     * @param string|bool $value
     * @param string $result
     */
    public function testSetExplicit($value, $result)
    {
        $feed = new Writer\Feed();
        $feed->setItunesExplicit($value);
        $this->assertEquals($result, $feed->getItunesExplicit());
    }

    /** @psalm-return array<array-key, array{0: bool|string, 1: string}> */
    public static function dataProviderForSetExplicit(): array
    {
        return [
            // Current behaviour
            [
                true,
                'true',
            ],
            [
                false,
                'false',
            ],
            // Old behaviour
            [
                'yes',
                'true',
            ],
            [
                'no',
                'false',
            ],
            [
                'clean',
                'false',
            ],
        ];
    }

    public function testSetExplicitThrowsExceptionOnUnknownTerm(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setItunesExplicit('abc');
    }

    public function testSetKeywords(): void
    {
        $feed  = new Writer\Feed();
        $words = [
            'a1',
            'a2',
            'a3',
            'a4',
            'a5',
            'a6',
            'a7',
            'a8',
            'a9',
            'a10',
            'a11',
            'a12',
        ];

        /** @psalm-suppress UnusedClosureParam */
        set_error_handler(static fn(int $errno, string $errstr): bool =>
            (bool) preg_match('/itunes:keywords/', $errstr), E_USER_DEPRECATED);
        $feed->setItunesKeywords($words);
        restore_error_handler();

        $this->assertEquals($words, $feed->getItunesKeywords());
    }

    public function testSetKeywordsThrowsExceptionIfMaxKeywordsExceeded(): void
    {
        $feed  = new Writer\Feed();
        $words = [
            'a1',
            'a2',
            'a3',
            'a4',
            'a5',
            'a6',
            'a7',
            'a8',
            'a9',
            'a10',
            'a11',
            'a12',
            'a13',
        ];

        /** @psalm-suppress UnusedClosureParam */
        set_error_handler(static fn(int $errno, string $errstr): bool =>
            (bool) preg_match('/itunes:keywords/', $errstr), E_USER_DEPRECATED);

        try {
            $this->expectException(ExceptionInterface::class);
            $feed->setItunesKeywords($words);
        } finally {
            restore_error_handler();
        }
    }

    public function testSetKeywordsThrowsExceptionIfFormattedKeywordsExceeds255CharLength(): void
    {
        $feed  = new Writer\Feed();
        $words = [
            str_repeat('a', 253),
            str_repeat('b', 2),
        ];

        /** @psalm-suppress UnusedClosureParam */
        set_error_handler(static fn(int $errno, string $errstr): bool =>
            (bool) preg_match('/itunes:keywords/', $errstr), E_USER_DEPRECATED);

        try {
            $this->expectException(ExceptionInterface::class);
            $feed->setItunesKeywords($words);
        } finally {
            restore_error_handler();
        }
    }

    public function testSetNewFeedUrl(): void
    {
        $feed = new Writer\Feed();
        $feed->setItunesNewFeedUrl('http://example.com/feed');
        $this->assertEquals('http://example.com/feed', $feed->getItunesNewFeedUrl());
    }

    public function testSetNewFeedUrlThrowsExceptionOnInvalidUri(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setItunesNewFeedUrl('http://');
    }

    public function testAddOwner(): void
    {
        $feed = new Writer\Feed();
        $feed->addItunesOwner(['name' => 'joe', 'email' => 'joe@example.com']);
        $this->assertEquals([['name' => 'joe', 'email' => 'joe@example.com']], $feed->getItunesOwners());
    }

    public function testAddOwners(): void
    {
        $feed = new Writer\Feed();
        $feed->addItunesOwners([['name' => 'joe', 'email' => 'joe@example.com']]);
        $this->assertEquals([['name' => 'joe', 'email' => 'joe@example.com']], $feed->getItunesOwners());
    }

    public function testSetSubtitle(): void
    {
        $feed = new Writer\Feed();
        $feed->setItunesSubtitle('abc');
        $this->assertEquals('abc', $feed->getItunesSubtitle());
    }

    public function testSetSubtitleThrowsExceptionWhenValueExceeds255Chars(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setItunesSubtitle(str_repeat('a', 256));
    }

    public function testSetSummary(): void
    {
        $feed = new Writer\Feed();
        $feed->setItunesSummary('abc');
        $this->assertEquals('abc', $feed->getItunesSummary());
    }

    public function testSetSummaryThrowsExceptionWhenValueExceeds4000Chars(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setItunesSummary(str_repeat('a', 4001));
    }

    /** @psalm-return array<string, array{0: mixed, 1: string}> */
    public static function invalidImageUrls(): array
    {
        $defaultExpectedMessage = 'valid URI';
        return [
            'null'                  => [null, $defaultExpectedMessage],
            'true'                  => [true, $defaultExpectedMessage],
            'false'                 => [false, $defaultExpectedMessage],
            'zero'                  => [0, $defaultExpectedMessage],
            'int'                   => [1, $defaultExpectedMessage],
            'zero-float'            => [0.0, $defaultExpectedMessage],
            'float'                 => [1.1, $defaultExpectedMessage],
            'string'                => ['scheme:/host.path', $defaultExpectedMessage],
            'invalid-extension-gif' => ['https://example.com/image.gif', 'file extension'],
            'invalid-extension-uc'  => ['https://example.com/image.PNG', 'file extension'],
            'array'                 => [['https://example.com/image.png'], $defaultExpectedMessage],
            'object'                => [(object) ['image' => 'https://example.com/image.png'], $defaultExpectedMessage],
        ];
    }

    /**
     * @dataProvider invalidImageUrls
     * @param mixed $url
     */
    public function testSetItunesImageRaisesExceptionForInvalidUrl($url, string $expectedMessage)
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $this->expectExceptionMessage($expectedMessage);
        $feed->setItunesImage($url);
    }

    /** @psalm-return array<string, array{0: string}> */
    public static function validImageUrls(): array
    {
        return [
            'jpg' => ['https://example.com/image.jpg'],
            'png' => ['https://example.com/image.png'],
        ];
    }

    /**
     * @dataProvider validImageUrls
     * @param string $url
     */
    public function testSetItunesImageSetsInternalDataWithValidUrl($url)
    {
        $feed = new Writer\Feed();
        $feed->setItunesImage($url);
        $this->assertEquals($url, $feed->getItunesImage());
    }

    /** @psalm-return array<string, array{0: mixed}> */
    public static function invalidPodcastTypes(): array
    {
        return [
            'null'       => [null],
            'true'       => [true],
            'false'      => [false],
            'zero'       => [0],
            'int'        => [1],
            'zero-float' => [0.0],
            'float'      => [1.1],
            'string'     => ['not-a-type'],
            'array'      => [['episodic']],
            'object'     => [(object) ['type' => 'episodic']],
        ];
    }

    /**
     * @dataProvider invalidPodcastTypes
     * @param mixed $type
     */
    public function testSetItunesTypeWithInvalidTypeRaisesException($type)
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $this->expectExceptionMessage('MUST be one of');
        $feed->setItunesType($type);
    }

    /** @psalm-return array<string, array{0: string}> */
    public static function validPodcastTypes(): array
    {
        return [
            'episodic' => ['episodic'],
            'serial'   => ['serial'],
        ];
    }

    /**
     * @dataProvider validPodcastTypes
     * @param mixed $type
     */
    public function testSetItunesTypeMutatesTypeWithValidData($type)
    {
        $feed = new Writer\Feed();
        $feed->setItunesType($type);
        $this->assertEquals($type, $feed->getItunesType());
    }

    /** @psalm-return array<string, array{0: mixed}> */
    public static function invalidCompleteStatuses(): array
    {
        return [
            'null'       => [null],
            'zero'       => [0],
            'int'        => [1],
            'zero-float' => [0.0],
            'float'      => [1.1],
            'string'     => ['not-a-status'],
            'array'      => [[true]],
            'object'     => [(object) ['complete' => true]],
        ];
    }

    /**
     * @dataProvider invalidCompleteStatuses
     * @param mixed $status
     */
    public function testSetItunesCompleteRaisesExceptionForInvalidStatus($status)
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $this->expectExceptionMessage('MUST be boolean');
        $feed->setItunesComplete($status);
    }

    public function testSetItunesCompleteWithTrueSetsDataInContainer(): void
    {
        $feed = new Writer\Feed();
        $feed->setItunesComplete(true);
        $this->assertEquals('Yes', $feed->getItunesComplete());
    }

    public function testSetItunesCompleteWithFalseDoesNotSetDataInContainer(): void
    {
        $feed = new Writer\Feed();
        $feed->setItunesComplete(false);
        $this->assertNull($feed->getItunesComplete());
    }
}
