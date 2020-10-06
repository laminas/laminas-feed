<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Writer\Extension\GooglePlayPodcast;

use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Exception\ExceptionInterface;
use PHPUnit\Framework\TestCase;

class FeedTest extends TestCase
{
    public function testSetBlock(): void
    {
        $feed = new Writer\Feed();
        $feed->setPlayPodcastBlock('yes');
        $this->assertEquals('yes', $feed->getPlayPodcastBlock());
    }

    public function testSetBlockThrowsExceptionOnNonAlphaValue(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setPlayPodcastBlock('123');
    }

    public function testSetBlockThrowsExceptionIfValueGreaterThan255CharsLength(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setPlayPodcastBlock(str_repeat('a', 256));
    }

    public function testAddAuthors(): void
    {
        $feed = new Writer\Feed();
        $feed->addPlayPodcastAuthors(['joe', 'jane']);
        $this->assertEquals(['joe', 'jane'], $feed->getPlayPodcastAuthors());
    }

    public function testAddAuthor(): void
    {
        $feed = new Writer\Feed();
        $feed->addPlayPodcastAuthor('joe');
        $this->assertEquals(['joe'], $feed->getPlayPodcastAuthors());
    }

    public function testAddAuthorThrowsExceptionIfValueGreaterThan255CharsLength(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->addPlayPodcastAuthor(str_repeat('a', 256));
    }

    public function testSetCategories(): void
    {
        $feed = new Writer\Feed();
        $cats = [
            'cat1',
            'cat2' => ['cat2-1', 'cat2-a&b'],
        ];
        $feed->setPlayPodcastCategories($cats);
        $this->assertEquals($cats, $feed->getPlayPodcastCategories());
    }

    public function testSetCategoriesThrowsExceptionIfAnyCatNameGreaterThan255CharsLength(): void
    {
        $feed = new Writer\Feed();
        $cats = [
            'cat1',
            'cat2' => ['cat2-1', str_repeat('a', 256)],
        ];

        $this->expectException(ExceptionInterface::class);
        $feed->setPlayPodcastCategories($cats);
    }

    public function testSetImageAsPngFile(): void
    {
        $feed = new Writer\Feed();
        $feed->setPlayPodcastImage('http://www.example.com/image.png');
        $this->assertEquals('http://www.example.com/image.png', $feed->getPlayPodcastImage());
    }

    public function testSetImageAsJpgFile(): void
    {
        $feed = new Writer\Feed();
        $feed->setPlayPodcastImage('http://www.example.com/image.jpg');
        $this->assertEquals('http://www.example.com/image.jpg', $feed->getPlayPodcastImage());
    }

    public function testSetImageThrowsExceptionOnInvalidUri(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setPlayPodcastImage('http://');
    }

    public function testSetExplicitToYes(): void
    {
        $feed = new Writer\Feed();
        $feed->setPlayPodcastExplicit('yes');
        $this->assertEquals('yes', $feed->getPlayPodcastExplicit());
    }

    public function testSetExplicitToNo(): void
    {
        $feed = new Writer\Feed();
        $feed->setPlayPodcastExplicit('no');
        $this->assertEquals('no', $feed->getPlayPodcastExplicit());
    }

    public function testSetExplicitToClean(): void
    {
        $feed = new Writer\Feed();
        $feed->setPlayPodcastExplicit('clean');
        $this->assertEquals('clean', $feed->getPlayPodcastExplicit());
    }

    public function testSetExplicitThrowsExceptionOnUnknownTerm(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setPlayPodcastExplicit('abc');
    }

    public function testSetDescription(): void
    {
        $feed = new Writer\Feed();
        $feed->setPlayPodcastDescription('abc');
        $this->assertEquals('abc', $feed->getPlayPodcastDescription());
    }

    public function testSetDescriptionThrowsExceptionWhenValueExceeds4000Chars(): void
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setPlayPodcastDescription(str_repeat('a', 4001));
    }

    public function invalidImageUrls()
    {
        return [
            'null' => [null],
            'true' => [true],
            'false' => [false],
            'zero' => [0],
            'int' => [1],
            'zero-float' => [0.0],
            'float' => [1.1],
            'string' => ['scheme:/host.path'],
            'array' => [['https://example.com/image.png']],
            'object' => [(object) ['image' => 'https://example.com/image.png']],
        ];
    }

    /**
     * @dataProvider invalidImageUrls
     *
     * @param mixed $url
     */
    public function testSetPlayPodcastImageRaisesExceptionForInvalidUrl($url)
    {
        $feed = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $feed->setPlayPodcastImage($url);
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
     *
     * @param string $url
     */
    public function testSetPlayPodcastImageSetsInternalDataWithValidUrl($url)
    {
        $feed = new Writer\Feed();
        $feed->setPlayPodcastImage($url);
        $this->assertEquals($url, $feed->getPlayPodcastImage());
    }
}
