<?php

namespace LaminasTest\Feed\Reader\Integration;

use Laminas\Feed\Reader;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Reader
 */
class PodcastRss2Test extends TestCase
{
    protected $feedSamplePath;

    protected function setUp(): void
    {
        Reader\Reader::reset();
        $this->feedSamplePath = dirname(__FILE__) . '/_files/podcast.xml';
    }

    /**
     * Feed level testing
     *
     */
    public function testGetsNewFeedUrl(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('http://newlocation.com/example.rss', $feed->getNewFeedUrl());
    }

    public function testGetsOwner(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('john.doe@example.com (John Doe)', $feed->getOwner());
    }

    public function testGetsCategories(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals([
            'Technology' => [
                'Gadgets' => null,
            ],
            'TV & Film'  => null,
        ], $feed->getItunesCategories());
    }

    public function testGetsTitle(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('All About Everything', $feed->getTitle());
    }

    public function testGetsCastAuthor(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('John Doe', $feed->getCastAuthor());
    }

    public function testGetsFeedBlock(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('no', $feed->getBlock());
    }

    public function testGetsCopyright(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('℗ & © 2005 John Doe & Family', $feed->getCopyright());
    }

    public function testGetsDescription(): void
    {
        $feed     = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $expected = 'All About Everything is a show about everything.
            Each week we dive into any subject known to man and talk
            about it as much as we can. Look for our Podcast in the
            iTunes Store';
        $expected = str_replace("\r\n", "\n", $expected);
        $this->assertEquals($expected, $feed->getDescription());
    }

    public function testGetsLanguage(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('en-us', $feed->getLanguage());
    }

    public function testGetsLink(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('http://www.example.com/podcasts/everything/index.html', $feed->getLink());
    }

    public function testGetsEncoding(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    public function testGetsFeedExplicit(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('yes', $feed->getExplicit());
    }

    public function testGetsEntryCount(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(3, $feed->count());
    }

    public function testGetsImage(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('http://example.com/podcasts/everything/AllAboutEverything.jpg', $feed->getItunesImage());
    }

    /**
     * Entry level testing
     *
     */
    public function testGetsEntryBlock(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('yes', $entry->getBlock());
    }

    public function testGetsEntryId(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://example.com/podcasts/archive/aae20050615.m4a', $entry->getId());
    }

    public function testGetsEntryTitle(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('Shake Shake Shake Your Spices', $entry->getTitle());
    }

    public function testGetsEntryCastAuthor(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('John Doe', $entry->getCastAuthor());
    }

    public function testGetsEntryExplicit(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('no', $entry->getExplicit());
    }

    public function testGetsSubtitle(): void
    {
        $feed     = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry    = $feed->current();
        $expected = 'A short primer on table spices
            ';
        $expected = str_replace("\r\n", "\n", $expected);
        $this->assertEquals($expected, $entry->getSubtitle());
    }

    public function testGetsSummary(): void
    {
        $feed     = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry    = $feed->current();
        $expected = 'This week we talk about salt and pepper
                shakers, comparing and contrasting pour rates,
                construction materials, and overall aesthetics. Come and
                join the party!';
        $expected = str_replace("\r\n", "\n", $expected);
        $this->assertEquals($expected, $entry->getSummary());
    }

    public function testGetsDuration(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('7:04', $entry->getDuration());
    }

    public function testGetsKeywords(): void
    {
        $feed     = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry    = $feed->current();
        $expected = 'salt, pepper, shaker, exciting
            ';
        $expected = str_replace("\r\n", "\n", $expected);

        set_error_handler(static function ($errno, $errstr) {
            return (bool) preg_match('/itunes:keywords/', $errstr);
        }, \E_USER_DEPRECATED);
        $keywords = $entry->getKeywords();
        restore_error_handler();

        $this->assertEquals($expected, $keywords);
    }

    public function testGetsEntryEncoding(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }

    public function testGetsEnclosure(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();

        $expected         = new stdClass();
        $expected->url    = 'http://example.com/podcasts/everything/AllAboutEverythingEpisode3.m4a';
        $expected->length = '8727310';
        $expected->type   = 'audio/x-m4a';

        $this->assertEquals($expected, $entry->getEnclosure());
    }

    public function testCanRetrieveEntryImage(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();

        $this->assertEquals(
            'https://www.example.com/podcasts/everything/episode.png',
            $entry->getItunesImage()
        );
    }

    public function testCanRetrievePodcastType(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('serial', $feed->getPodcastType());
    }

    public function testPodcastTypeIsEpisodicWhenNoTagPresent(): void
    {
        $feedSamplePath = dirname(__FILE__) . '/_files/podcast-no-type.xml';
        $feed           = Reader\Reader::importString(
            file_get_contents($feedSamplePath)
        );
        $this->assertEquals('episodic', $feed->getPodcastType());
    }

    public function testIsNotCompleteByDefault(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertFalse($feed->isComplete());
    }

    public function testIsCompleteReturnsTrueWhenTagValueIsYes(): void
    {
        $feedSamplePath = dirname(__FILE__) . '/_files/podcast-complete.xml';
        $feed           = Reader\Reader::importString(
            file_get_contents($feedSamplePath)
        );
        $this->assertTrue($feed->isComplete());
    }

    public function testIsCompleteReturnsFalseWhenTagValueIsSomethingOtherThanYes(): void
    {
        $feedSamplePath = dirname(__FILE__) . '/_files/podcast-incomplete.xml';
        $feed           = Reader\Reader::importString(
            file_get_contents($feedSamplePath)
        );
        $this->assertFalse($feed->isComplete());
    }

    public function testGetEpisodeReturnsNullIfNoTagPresent(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertNull($entry->getEpisode());
    }

    public function testGetEpisodeTypeReturnsFullIfNoTagPresent(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();

        $this->assertEquals('full', $entry->getEpisodeType());
    }

    public function testGetEpisodeReturnsValueWhenTagPresent(): void
    {
        $feedSamplePath = dirname(__FILE__) . '/_files/podcast-episode.xml';
        $feed           = Reader\Reader::importString(
            file_get_contents($feedSamplePath)
        );
        $entry          = $feed->current();
        $this->assertEquals(10, $entry->getEpisode());
    }

    public function testGetEpisodeTypeReturnsValueWhenTagPresent(): void
    {
        $feedSamplePath = dirname(__FILE__) . '/_files/podcast-episode.xml';
        $feed           = Reader\Reader::importString(
            file_get_contents($feedSamplePath)
        );
        $entry          = $feed->current();
        $this->assertEquals('bonus', $entry->getEpisodeType());
    }

    public function testIsClosedCaptionedReturnsTrueWhenEpisodeDefinesItWithValueYes(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertTrue($entry->isClosedCaptioned());
    }

    public function testIsClosedCaptionedReturnsFalseWhenEpisodeDefinesItWithValueOtherThanYes(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $feed->next(); // Second entry uses "No" as value
        $entry = $feed->current();
        $this->assertFalse($entry->isClosedCaptioned());
    }

    public function testIsClosedCaptionedReturnsFalseWhenEpisodeDoesNotDefineIt(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $feed->next();
        $feed->next(); // Third entry does not define it
        $entry = $feed->current();
        $this->assertFalse($entry->isClosedCaptioned());
    }

    public function testGetSeasonReturnsNullIfNoTagPresent(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertNull($entry->getSeason());
    }

    public function testGetSeasonReturnsValueWhenTagPresent(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $feed->next(); // second item defines the tag
        $entry = $feed->current();
        $this->assertEquals(3, $entry->getSeason());
    }
}
