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
class FeedTest extends TestCase
{
    public function testSetBlock()
    {
        $feed = new Writer\Feed;
        $feed->setItunesBlock('yes');
        $this->assertEquals('yes', $feed->getItunesBlock());
    }

    public function testSetBlockThrowsExceptionOnNonAlphaValue()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $feed->setItunesBlock('123');
    }

    public function testSetBlockThrowsExceptionIfValueGreaterThan255CharsLength()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $feed->setItunesBlock(str_repeat('a', 256));
    }

    public function testAddAuthors()
    {
        $feed = new Writer\Feed;
        $feed->addItunesAuthors(['joe', 'jane']);
        $this->assertEquals(['joe', 'jane'], $feed->getItunesAuthors());
    }

    public function testAddAuthor()
    {
        $feed = new Writer\Feed;
        $feed->addItunesAuthor('joe');
        $this->assertEquals(['joe'], $feed->getItunesAuthors());
    }

    public function testAddAuthorThrowsExceptionIfValueGreaterThan255CharsLength()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $feed->addItunesAuthor(str_repeat('a', 256));
    }

    public function testSetCategories()
    {
        $feed = new Writer\Feed;
        $cats = [
            'cat1',
            'cat2' => ['cat2-1', 'cat2-a&b']
        ];
        $feed->setItunesCategories($cats);
        $this->assertEquals($cats, $feed->getItunesCategories());
    }

    public function testSetCategoriesThrowsExceptionIfAnyCatNameGreaterThan255CharsLength()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $cats = [
            'cat1',
            'cat2' => ['cat2-1', str_repeat('a', 256)]
        ];
        $feed->setItunesCategories($cats);
        $this->assertEquals($cats, $feed->getItunesAuthors());
    }

    public function testSetImageAsPngFile()
    {
        $feed = new Writer\Feed;
        $feed->setItunesImage('http://www.example.com/image.png');
        $this->assertEquals('http://www.example.com/image.png', $feed->getItunesImage());
    }

    public function testSetImageAsJpgFile()
    {
        $feed = new Writer\Feed;
        $feed->setItunesImage('http://www.example.com/image.jpg');
        $this->assertEquals('http://www.example.com/image.jpg', $feed->getItunesImage());
    }

    public function testSetImageThrowsExceptionOnInvalidUri()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $feed->setItunesImage('http://');
    }

    public function testSetImageThrowsExceptionOnInvalidImageExtension()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $feed->setItunesImage('http://www.example.com/image.gif');
    }

    public function testSetDurationAsSeconds()
    {
        $feed = new Writer\Feed;
        $feed->setItunesDuration(23);
        $this->assertEquals(23, $feed->getItunesDuration());
    }

    public function testSetDurationAsMinutesAndSeconds()
    {
        $feed = new Writer\Feed;
        $feed->setItunesDuration('23:23');
        $this->assertEquals('23:23', $feed->getItunesDuration());
    }

    public function testSetDurationAsHoursMinutesAndSeconds()
    {
        $feed = new Writer\Feed;
        $feed->setItunesDuration('23:23:23');
        $this->assertEquals('23:23:23', $feed->getItunesDuration());
    }

    public function testSetDurationThrowsExceptionOnUnknownFormat()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $feed->setItunesDuration('abc');
    }

    public function testSetDurationThrowsExceptionOnInvalidSeconds()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $feed->setItunesDuration('23:456');
    }

    public function testSetDurationThrowsExceptionOnInvalidMinutes()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $feed->setItunesDuration('23:234:45');
    }

    public function testSetExplicitToYes()
    {
        $feed = new Writer\Feed;
        $feed->setItunesExplicit('yes');
        $this->assertEquals('yes', $feed->getItunesExplicit());
    }

    public function testSetExplicitToNo()
    {
        $feed = new Writer\Feed;
        $feed->setItunesExplicit('no');
        $this->assertEquals('no', $feed->getItunesExplicit());
    }

    public function testSetExplicitToClean()
    {
        $feed = new Writer\Feed;
        $feed->setItunesExplicit('clean');
        $this->assertEquals('clean', $feed->getItunesExplicit());
    }

    public function testSetExplicitThrowsExceptionOnUnknownTerm()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $feed->setItunesExplicit('abc');
    }

    public function testSetKeywords()
    {
        $feed = new Writer\Feed;
        $words = [
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12'
        ];
        $feed->setItunesKeywords($words);
        $this->assertEquals($words, $feed->getItunesKeywords());
    }

    public function testSetKeywordsThrowsExceptionIfMaxKeywordsExceeded()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $words = [
            'a1', 'a2', 'a3', 'a4', 'a5', 'a6', 'a7', 'a8', 'a9', 'a10', 'a11', 'a12', 'a13'
        ];
        $feed->setItunesKeywords($words);
    }

    public function testSetKeywordsThrowsExceptionIfFormattedKeywordsExceeds255CharLength()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $words = [
            str_repeat('a', 253), str_repeat('b', 2)
        ];
        $feed->setItunesKeywords($words);
    }

    public function testSetNewFeedUrl()
    {
        $feed = new Writer\Feed;
        $feed->setItunesNewFeedUrl('http://example.com/feed');
        $this->assertEquals('http://example.com/feed', $feed->getItunesNewFeedUrl());
    }

    public function testSetNewFeedUrlThrowsExceptionOnInvalidUri()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $feed->setItunesNewFeedUrl('http://');
    }

    public function testAddOwner()
    {
        $feed = new Writer\Feed;
        $feed->addItunesOwner(['name' => 'joe', 'email' => 'joe@example.com']);
        $this->assertEquals([['name' => 'joe', 'email' => 'joe@example.com']], $feed->getItunesOwners());
    }

    public function testAddOwners()
    {
        $feed = new Writer\Feed;
        $feed->addItunesOwners([['name' => 'joe', 'email' => 'joe@example.com']]);
        $this->assertEquals([['name' => 'joe', 'email' => 'joe@example.com']], $feed->getItunesOwners());
    }

    public function testSetSubtitle()
    {
        $feed = new Writer\Feed;
        $feed->setItunesSubtitle('abc');
        $this->assertEquals('abc', $feed->getItunesSubtitle());
    }

    public function testSetSubtitleThrowsExceptionWhenValueExceeds255Chars()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $feed->setItunesSubtitle(str_repeat('a', 256));
    }

    public function testSetSummary()
    {
        $feed = new Writer\Feed;
        $feed->setItunesSummary('abc');
        $this->assertEquals('abc', $feed->getItunesSummary());
    }

    public function testSetSummaryThrowsExceptionWhenValueExceeds4000Chars()
    {
        $this->expectException(ExceptionInterface::class);
        $feed = new Writer\Feed;
        $feed->setItunesSummary(str_repeat('a', 4001));
    }
}
