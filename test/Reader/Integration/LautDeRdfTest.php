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
class LautDeRdfTest extends TestCase
{
    protected $feedSamplePath;

    protected function setUp(): void
    {
        Reader\Reader::reset();
        $this->feedSamplePath = dirname(__FILE__) . '/_files/laut.de-rdf.xml';
    }

    /**
     * Feed level testing
     *
     */
    public function testGetsTitle(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('laut.de - news', $feed->getTitle());
    }

    public function testGetsAuthors(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals([['name' => 'laut.de']], (array) $feed->getAuthors());
    }

    public function testGetsSingleAuthor(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(['name' => 'laut.de'], $feed->getAuthor());
    }

    public function testGetsCopyright(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('Copyright © 2004 laut.de', $feed->getCopyright());
    }

    public function testGetsDescription(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('laut.de: aktuelle News', $feed->getDescription());
    }

    public function testGetsLanguage(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLink(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('http://www.laut.de', $feed->getLink());
    }

    public function testGetsEncoding(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $this->assertEquals('ISO-8859-1', $feed->getEncoding());
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
        $this->assertEquals('http://www.laut.de/vorlaut/news/2009/07/04/22426/index.htm', $entry->getId());
    }

    public function testGetsEntryTitle(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('Angelika Express: MySpace-Aus wegen Sido-Werbung', $entry->getTitle());
    }

    public function testGetsEntryAuthors(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals([['name' => 'laut.de']], (array) $entry->getAuthors());
    }

    public function testGetsEntrySingleAuthor(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(['name' => 'laut.de'], $entry->getAuthor());
    }

    // Technically, the next two tests should not pass. However the source feed has an encoding
    // problem - it's stated as ISO-8859-1 but sent as UTF-8. The result is that a) it's
    // broken itself, or b) We should consider a fix in the future for similar feeds such
    // as using a more limited XML based decoding method (not html_entity_decode())

    public function testGetsEntryDescription(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(
            'Schon lÃ¤nger haderten die KÃ¶lner mit der Plattform des "fiesen Rupert Murdoch". '
            . 'Das Fass zum Ãberlaufen brachte aber ein Werbebanner von Deutschrapper Sido.',
            $entry->getDescription()
        );
    }

    public function testGetsEntryContent(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(
            'Schon lÃ¤nger haderten die KÃ¶lner mit der Plattform des "fiesen Rupert Murdoch". '
            . 'Das Fass zum Ãberlaufen brachte aber ein Werbebanner von Deutschrapper Sido.',
            $entry->getContent()
        );
    }

    public function testGetsEntryLinks(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(['http://www.laut.de/vorlaut/news/2009/07/04/22426/index.htm'], $entry->getLinks());
    }

    public function testGetsEntryLink(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('http://www.laut.de/vorlaut/news/2009/07/04/22426/index.htm', $entry->getLink());
    }

    public function testGetsEntryPermaLink(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals(
            'http://www.laut.de/vorlaut/news/2009/07/04/22426/index.htm',
            $entry->getPermaLink()
        );
    }

    public function testGetsEntryEncoding(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath)
        );
        $entry = $feed->current();
        $this->assertEquals('ISO-8859-1', $entry->getEncoding());
    }
}
