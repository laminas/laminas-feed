<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Reader\Integration;

use Laminas\Feed\Reader;
use PHPUnit\Framework\TestCase;
use stdClass;

class GoolePlayPodcastRss2Test extends TestCase
{
    protected $feedSamplePath;

    protected function setUp(): void
    {
        Reader\Reader::reset();
        $this->feedSamplePath = dirname(__FILE__) . '/_files/google-podcast.xml';
    }

    /**
     * Feed level testing
     */
    public function testGetsNewFeedUrl()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('http://newlocation.com/example.rss', $feed->getNewFeedUrl());
    }

    public function testGetsOwner()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('john.doe@example.com (John Doe)', $feed->getOwner());
    }

    public function testGetsCategories()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals([
            'Technology' => [
                'Gadgets' => null,
            ],
            'TV & Film'  => null,
        ], $feed->getPlayPodcastCategories());
    }

    public function testGetsTitle()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('All About Everything', $feed->getTitle());
    }

    public function testGetsCastAuthor()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('John Doe', $feed->getPlayPodcastAuthor());
    }

    public function testGetsFeedBlock()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('no', $feed->getPlayPodcastBlock());
    }

    public function testGetsCopyright()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('℗ & © 2005 John Doe & Family', $feed->getCopyright());
    }

    public function testGetsDescription()
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

    public function testGetsPodcastDescription()
    {
        $feed     = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $expected = 'All About Everything is a show about everything.
            Each week we dive into any subject known to man and talk
            about it as much as we can. Look for our Podcast in the
            iTunes Store';
        $expected = str_replace("\r\n", "\n", $expected);
        $this->assertEquals($expected, $feed->getPlayPodcastDescription());
    }

    public function testGetsLanguage()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('en-us', $feed->getLanguage());
    }

    public function testGetsLink()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('http://www.example.com/podcasts/everything/index.html', $feed->getLink());
    }

    public function testGetsEncoding()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    public function testGetsFeedExplicit()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('yes', $feed->getPlayPodcastExplicit());
    }

    public function testGetsEntryCount()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(3, $feed->count());
    }

    public function testGetsImage()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(
            'http://example.com/podcasts/everything/AllAboutEverything.jpg',
            $feed->getPlayPodcastImage()
        );
    }

    /**
     * Entry level testing
     */
    public function testGetsEntryBlock()
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('yes', $entry->getPlayPodcastBlock());
    }

    public function testGetsEntryId()
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://example.com/podcasts/archive/aae20050615.m4a', $entry->getId());
    }

    public function testGetsEntryTitle()
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('Shake Shake Shake Your Spices', $entry->getTitle());
    }

    public function testGetsEntryCastAuthor()
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('John Doe', $entry->getCastAuthor());
    }

    public function testGetsEntryExplicit()
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('no', $entry->getPlayPodcastExplicit());
    }

    public function testGetsSubtitle()
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

    public function testGetsEpisodeDescription()
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
        $this->assertEquals($expected, $entry->getPlayPodcastDescription());
    }

    public function testGetsDuration()
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('7:04', $entry->getDuration());
    }

    public function testGetsEntryEncoding()
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }

    public function testGetsEnclosure()
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

    public function testCanRetrieveEntryImage()
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

    public function testCanRetrievePodcastType()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('serial', $feed->getPodcastType());
    }

    public function testPodcastTypeIsEpisodicWhenNoTagPresent()
    {
        $feedSamplePath = dirname(__FILE__) . '/_files/google-podcast-no-type.xml';
        $feed           = Reader\Reader::importString(
            file_get_contents($feedSamplePath)
        );
        $this->assertEquals('episodic', $feed->getPodcastType());
    }

    public function testIsNotCompleteByDefault()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertFalse($feed->isComplete());
    }

    public function testIsCompleteReturnsTrueWhenTagValueIsYes()
    {
        $feedSamplePath = dirname(__FILE__) . '/_files/google-podcast-complete.xml';
        $feed           = Reader\Reader::importString(
            file_get_contents($feedSamplePath)
        );
        $this->assertTrue($feed->isComplete());
    }

    public function testIsCompleteReturnsFalseWhenTagValueIsSomethingOtherThanYes()
    {
        $feedSamplePath = dirname(__FILE__) . '/_files/google-podcast-incomplete.xml';
        $feed           = Reader\Reader::importString(
            file_get_contents($feedSamplePath)
        );
        $this->assertFalse($feed->isComplete());
    }

    public function testGetEpisodeReturnsNullIfNoTagPresent()
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertNull($entry->getEpisode());
    }

    public function testGetEpisodeTypeReturnsFullIfNoTagPresent()
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();

        $this->assertEquals('full', $entry->getEpisodeType());
    }

    public function testGetEpisodeReturnsValueWhenTagPresent()
    {
        $feedSamplePath = dirname(__FILE__) . '/_files/google-podcast-episode.xml';
        $feed           = Reader\Reader::importString(
            file_get_contents($feedSamplePath)
        );
        $entry          = $feed->current();
        $this->assertEquals(10, $entry->getEpisode());
    }

    public function testGetEpisodeTypeReturnsValueWhenTagPresent()
    {
        $feedSamplePath = dirname(__FILE__) . '/_files/google-podcast-episode.xml';
        $feed           = Reader\Reader::importString(
            file_get_contents($feedSamplePath)
        );
        $entry          = $feed->current();
        $this->assertEquals('bonus', $entry->getEpisodeType());
    }

    public function testIsClosedCaptionedReturnsTrueWhenEpisodeDefinesItWithValueYes()
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertTrue($entry->isClosedCaptioned());
    }

    public function testIsClosedCaptionedReturnsFalseWhenEpisodeDefinesItWithValueOtherThanYes()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $feed->next(); // Second entry uses "No" as value
        $entry = $feed->current();
        $this->assertFalse($entry->isClosedCaptioned());
    }

    public function testIsClosedCaptionedReturnsFalseWhenEpisodeDoesNotDefineIt()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $feed->next();
        $feed->next(); // Third entry does not define it
        $entry = $feed->current();
        $this->assertFalse($entry->isClosedCaptioned());
    }

    public function testGetSeasonReturnsNullIfNoTagPresent()
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertNull($entry->getSeason());
    }

    public function testGetSeasonReturnsValueWhenTagPresent()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $feed->next(); // second item defines the tag
        $entry = $feed->current();
        $this->assertEquals(3, $entry->getSeason());
    }
}
