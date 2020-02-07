<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Writer\Extension\ITunes;

use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Exception\ExceptionInterface;
use PHPUnit\Framework\TestCase;

/**
* @group Laminas_Feed
* @group Laminas_Feed_Writer
*/
class EntryTest extends TestCase
{
    public function testSetBlock()
    {
        $entry = new Writer\Entry;
        $entry->setItunesBlock('yes');
        $this->assertEquals('yes', $entry->getItunesBlock());
    }

    public function testSetBlockThrowsExceptionOnNonAlphaValue()
    {
        $entry = new Writer\Entry;

        $this->expectException(ExceptionInterface::class);
        $entry->setItunesBlock('123');
    }

    public function testSetBlockThrowsExceptionIfValueGreaterThan255CharsLength()
    {
        $entry = new Writer\Entry;

        $this->expectException(ExceptionInterface::class);
        $entry->setItunesBlock(str_repeat('a', 256));
    }

    public function testAddAuthors()
    {
        $entry = new Writer\Entry;
        $entry->addItunesAuthors(['joe', 'jane']);
        $this->assertEquals(['joe', 'jane'], $entry->getItunesAuthors());
    }

    public function testAddAuthor()
    {
        $entry = new Writer\Entry;
        $entry->addItunesAuthor('joe');
        $this->assertEquals(['joe'], $entry->getItunesAuthors());
    }

    public function testAddAuthorThrowsExceptionIfValueGreaterThan255CharsLength()
    {
        $entry = new Writer\Entry;

        $this->expectException(ExceptionInterface::class);
        $entry->addItunesAuthor(str_repeat('a', 256));
    }

    public function testSetDurationAsSeconds()
    {
        $entry = new Writer\Entry;
        $entry->setItunesDuration(23);
        $this->assertEquals(23, $entry->getItunesDuration());
    }

    public function testSetDurationAsMinutesAndSeconds()
    {
        $entry = new Writer\Entry;
        $entry->setItunesDuration('23:23');
        $this->assertEquals('23:23', $entry->getItunesDuration());
    }

    public function testSetDurationAsHoursMinutesAndSeconds()
    {
        $entry = new Writer\Entry;
        $entry->setItunesDuration('23:23:23');
        $this->assertEquals('23:23:23', $entry->getItunesDuration());
    }

    public function testSetDurationThrowsExceptionOnUnknownFormat()
    {
        $entry = new Writer\Entry;

        $this->expectException(ExceptionInterface::class);
        $entry->setItunesDuration('abc');
    }

    public function testSetDurationThrowsExceptionOnInvalidSeconds()
    {
        $entry = new Writer\Entry;

        $this->expectException(ExceptionInterface::class);
        $entry->setItunesDuration('23:456');
    }

    public function testSetDurationThrowsExceptionOnInvalidMinutes()
    {
        $entry = new Writer\Entry;

        $this->expectException(ExceptionInterface::class);
        $entry->setItunesDuration('23:234:45');
    }

    public function testSetExplicitToYes()
    {
        $entry = new Writer\Entry;
        $entry->setItunesExplicit('yes');
        $this->assertEquals('yes', $entry->getItunesExplicit());
    }

    public function testSetExplicitToNo()
    {
        $entry = new Writer\Entry;
        $entry->setItunesExplicit('no');
        $this->assertEquals('no', $entry->getItunesExplicit());
    }

    public function testSetExplicitToClean()
    {
        $entry = new Writer\Entry;
        $entry->setItunesExplicit('clean');
        $this->assertEquals('clean', $entry->getItunesExplicit());
    }

    public function testSetExplicitThrowsExceptionOnUnknownTerm()
    {
        $entry = new Writer\Entry;

        $this->expectException(ExceptionInterface::class);
        $entry->setItunesExplicit('abc');
    }

    public function testSetKeywords()
    {
        $entry = new Writer\Entry;
        $words = [
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12'
        ];

        set_error_handler(function ($errno, $errstr) {
            return (bool) preg_match('/itunes:keywords/', $errstr);
        }, \E_USER_DEPRECATED);
        $entry->setItunesKeywords($words);
        restore_error_handler();

        $this->assertEquals($words, $entry->getItunesKeywords());
    }

    public function testSetKeywordsThrowsExceptionIfMaxKeywordsExceeded()
    {
        $entry = new Writer\Entry;
        $words = [
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12', 'a13'
        ];

        set_error_handler(function ($errno, $errstr) {
            return (bool) preg_match('/itunes:keywords/', $errstr);
        }, \E_USER_DEPRECATED);

        try {
            $this->expectException(ExceptionInterface::class);
            $entry->setItunesKeywords($words);
        } finally {
            restore_error_handler();
        }
    }

    public function testSetKeywordsThrowsExceptionIfFormattedKeywordsExceeds255CharLength()
    {
        $entry = new Writer\Entry;
        $words = [
            str_repeat('a', 253), str_repeat('b', 2)
        ];

        set_error_handler(function ($errno, $errstr) {
            return (bool) preg_match('/itunes:keywords/', $errstr);
        }, \E_USER_DEPRECATED);

        try {
            $this->expectException(ExceptionInterface::class);
            $entry->setItunesKeywords($words);
        } finally {
            restore_error_handler();
        }
    }

    public function testSetTitle()
    {
        $entry = new Writer\Entry;
        $entry->setItunesTitle('abc');
        $this->assertEquals('abc', $entry->getItunesTitle());
    }

    public function testSetTitleThrowsExceptionWhenValueExceeds255Chars()
    {
        $entry = new Writer\Entry;

        $this->expectException(ExceptionInterface::class);
        $entry->setItunesTitle(str_repeat('a', 256));
    }

    public function testSetSubtitle()
    {
        $entry = new Writer\Entry;
        $entry->setItunesSubtitle('abc');
        $this->assertEquals('abc', $entry->getItunesSubtitle());
    }

    public function testSetSubtitleThrowsExceptionWhenValueExceeds255Chars()
    {
        $entry = new Writer\Entry;

        $this->expectException(ExceptionInterface::class);
        $entry->setItunesSubtitle(str_repeat('a', 256));
    }

    public function testSetSummary()
    {
        $entry = new Writer\Entry;
        $entry->setItunesSummary('abc');
        $this->assertEquals('abc', $entry->getItunesSummary());
    }

    public function testSetSummaryThrowsExceptionWhenValueExceeds255Chars()
    {
        $entry = new Writer\Entry;

        $this->expectException(ExceptionInterface::class);
        $entry->setItunesSummary(str_repeat('a', 4001));
    }

    public function invalidImageUrls()
    {
        return [
            'null'                  => [null],
            'true'                  => [true],
            'false'                 => [false],
            'zero'                  => [0],
            'int'                   => [1],
            'zero-float'            => [0.0],
            'float'                 => [1.1],
            'string'                => ['scheme:/host.path'],
            'invalid-extension-gif' => ['https://example.com/image.gif', 'file extension'],
            'invalid-extension-uc'  => ['https://example.com/image.PNG', 'file extension'],
            'array'                 => [['https://example.com/image.png']],
            'object'                => [(object) ['image' => 'https://example.com/image.png']],
        ];
    }

    /**
     * @dataProvider invalidImageUrls
     * @param mixed $url
     * @param string $expectedMessage
     */
    public function testSetItunesImageRaisesExceptionForInvalidUrl($url, $expectedMessage = 'valid URI')
    {
        $entry = new Writer\Entry();

        $this->expectException(ExceptionInterface::class);
        $this->expectExceptionMessage($expectedMessage);
        $entry->setItunesImage($url);
    }

    public function validImageUrls()
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
        $entry = new Writer\Entry();
        $entry->setItunesImage($url);
        $this->assertEquals($url, $entry->getItunesImage());
    }

    public function nonNumericEpisodeNumbers()
    {
        return [
            'null'       => [null],
            'true'       => [true],
            'false'      => [false],
            'zero-float' => [0.000],
            'float'      => [1.1],
            'string'     => ['not-a-number'],
            'array'      => [[1]],
            'object'     => [(object) ['number' => 1]],
        ];
    }

    /**
     * @dataProvider nonNumericEpisodeNumbers
     * @param mixed $number
     */
    public function testSetEpisodeRaisesExceptionForNonNumericEpisodeNumbers($number)
    {
        $entry = new Writer\Entry();

        $this->expectException(ExceptionInterface::class);
        $this->expectExceptionMessage('may only be an integer');
        $entry->setItunesEpisode($number);
    }

    public function testSetEpisodeSetsNumberInEntry()
    {
        $entry = new Writer\Entry();
        $entry->setItunesEpisode(42);
        $this->assertEquals(42, $entry->getItunesEpisode());
    }

    public function invalidEpisodeTypes()
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
            'array'      => [['full']],
            'object'     => [(object) ['type' => 'full']],
        ];
    }

    /**
     * @dataProvider invalidEpisodeTypes
     * @param mixed $type
     */
    public function testSetEpisodeTypeRaisesExceptionForInvalidTypes($type)
    {
        $entry = new Writer\Entry();

        $this->expectException(ExceptionInterface::class);
        $this->expectExceptionMessage('MUST be one of');
        $entry->setItunesEpisodeType($type);
    }

    public function validEpisodeTypes()
    {
        return [
            'full'    => ['full'],
            'trailer' => ['trailer'],
            'bonus'   => ['bonus'],
        ];
    }

    /**
     * @dataProvider validEpisodeTypes
     * @param string $type
     */
    public function testEpisodeTypeMaybeMutatedWithAcceptedValues($type)
    {
        $entry = new Writer\Entry();
        $entry->setItunesEpisodeType($type);
        $this->assertEquals($type, $entry->getItunesEpisodeType());
    }

    public function invalidClosedCaptioningFlags()
    {
        return [
            'null'       => [null],
            'zero'       => [0],
            'int'        => [1],
            'zero-float' => [0.0],
            'float'      => [1.1],
            'string'     => ['Yes'],
            'array'      => [['Yes']],
            'object'     => [(object) ['isClosedCaptioned' => 'Yes']],
        ];
    }

    /**
     * @dataProvider invalidClosedCaptioningFlags
     * @param mixed $status
     */
    public function testSettingClosedCaptioningToNonBooleanRaisesException($status)
    {
        $entry = new Writer\Entry();

        $this->expectException(ExceptionInterface::class);
        $this->expectExceptionMessage('MUST be a boolean');
        $entry->setItunesIsClosedCaptioned($status);
    }

    public function testSettingClosedCaptioningToFalseDoesNothing()
    {
        $entry = new Writer\Entry();
        $entry->setItunesIsClosedCaptioned(false);
        $this->assertNull($entry->getItunesIsClosedCaptioned());
    }

    public function testSettingClosedCaptioningToTrueUpdatesContainer()
    {
        $entry = new Writer\Entry();
        $entry->setItunesIsClosedCaptioned(true);
        $this->assertTrue($entry->getItunesIsClosedCaptioned());
    }

    /**
     * @dataProvider nonNumericEpisodeNumbers
     * @param mixed $number
     */
    public function testSetSeasonRaisesExceptionForNonNumericSeasonNumbers($number)
    {
        $entry = new Writer\Entry();

        $this->expectException(ExceptionInterface::class);
        $this->expectExceptionMessage('may only be an integer');
        $entry->setItunesSeason($number);
    }

    public function testSetSeasonSetsNumberInEntry()
    {
        $entry = new Writer\Entry();
        $entry->setItunesSeason(42);
        $this->assertEquals(42, $entry->getItunesSeason());
    }
}
