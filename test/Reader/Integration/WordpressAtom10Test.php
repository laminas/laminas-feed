<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Reader\Integration;

use Laminas\Feed\Reader;
use PHPUnit\Framework\TestCase;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Reader
 */
class WordpressAtom10Test extends TestCase
{
    protected $feedSamplePath;

    protected function setUp(): void
    {
        Reader\Reader::reset();
        $this->feedSamplePath = dirname(__FILE__) . '/_files/wordpress-atom10.xml';
    }

    public function testGetsTitle(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('Norm 2782', $feed->getTitle());
    }

    public function testGetsAuthors(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals([
            ['name' => 'norm2782', 'uri' => 'http://www.norm2782.com'],
        ], (array) $feed->getAuthors());
    }

    public function testGetsSingleAuthor(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(['name' => 'norm2782', 'uri' => 'http://www.norm2782.com'], $feed->getAuthor());
    }

    public function testGetsCopyright(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsDescription(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('Why are you here?', $feed->getDescription());
    }

    public function testGetsLanguage(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('en', $feed->getLanguage());
    }

    public function testGetsLink(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('http://www.norm2782.com', $feed->getLink());
    }

    public function testGetsEncoding(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    public function testGetsEntryCount(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(10, $feed->count());
    }

    /**
     * Entry level testing
     *
     */
    public function testGetsEntryId(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.norm2782.com/?p=114', $entry->getId());
    }

    public function testGetsEntryTitle(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();

        /**
         * Note: The three dots below is actually a single Unicode character
         * called the "three dot leader". Don't replace in error!
         */
        $this->assertEquals('Wth&#8230; reading books?', $entry->getTitle());
    }

    public function testGetsEntryAuthors(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals([['name' => 'norm2782', 'uri' => 'http://www.norm2782.com']], (array) $entry->getAuthors());
    }

    public function testGetsEntrySingleAuthor(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(['name' => 'norm2782', 'uri' => 'http://www.norm2782.com'], $entry->getAuthor());
    }

    public function testGetsEntryDescription(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();

        /**
         * Note: "’" is not the same as "'" - don't replace in error
         */
        // @codingStandardsIgnoreStart
        $this->assertEquals('Being in New Zealand does strange things to a person. Everybody who knows me, knows I don&#8217;t much like that crazy invention called a Book. However, being here I&#8217;ve already finished 4 books, all of which I can highly recommend.'."\n\n".'Agile Software Development with Scrum, by Ken Schwaber and Mike Beedle'."\n".'Domain-Driven Design: Tackling Complexity in the [...]', $entry->getDescription());
        // @codingStandardsIgnoreEnd
    }

    public function testGetsEntryContent(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        // @codingStandardsIgnoreStart
        $this->assertEquals('<p>Being in New Zealand does strange things to a person. Everybody who knows me, knows I don&#8217;t much like that crazy invention called a Book. However, being here I&#8217;ve already finished 4 books, all of which I can highly recommend.</p><ul><li><a href="http://www.amazon.com/Agile-Software-Development-Scrum/dp/0130676349/">Agile Software Development with Scrum, by Ken Schwaber and Mike Beedle</a></li><li><a href="http://www.amazon.com/Domain-Driven-Design-Tackling-Complexity-Software/dp/0321125215/">Domain-Driven Design: Tackling Complexity in the Heart of Software, by Eric Evans</a></li><li><a href="http://www.amazon.com/Enterprise-Application-Architecture-Addison-Wesley-Signature/dp/0321127420/">Patterns of Enterprise Application Architecture, by Martin Fowler</a></li><li><a href="http://www.amazon.com/Refactoring-Improving-Existing-Addison-Wesley-Technology/dp/0201485672/">Refactoring: Improving the Design of Existing Code by Martin Fowler</a></li></ul><p>Next up: <a href="http://www.amazon.com/Design-Patterns-Object-Oriented-Addison-Wesley-Professional/dp/0201633612/">Design Patterns: Elements of Reusable Object-Oriented Software, by the Gang of Four</a>. Yes, talk about classics and shame on me for not having ordered it sooner! Also reading <a href="http://www.amazon.com/Implementation-Patterns-Addison-Wesley-Signature-Kent/dp/0321413091/">Implementation Patterns, by Kent Beck</a> at the moment.</p>', str_replace("\n", '', $entry->getContent()));
        // @codingStandardsIgnoreEnd
    }

    public function testGetsEntryLinks(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(['http://www.norm2782.com/2009/03/wth-reading-books/'], $entry->getLinks());
    }

    public function testGetsEntryLink(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.norm2782.com/2009/03/wth-reading-books/', $entry->getLink());
    }

    public function testGetsEntryPermaLink(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(
            'http://www.norm2782.com/2009/03/wth-reading-books/',
            $entry->getPermaLink()
        );
    }

    public function testGetsEntryEncoding(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }
}
