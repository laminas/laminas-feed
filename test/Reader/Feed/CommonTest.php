<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Reader\Feed;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Laminas\Feed\Reader;
use Laminas\Feed\Reader\Extension\Atom\Feed;
use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function str_replace;

/**
 * @group Laminas_Feed
 * @group Reader\Reader
 */
class CommonTest extends TestCase
{
    /** @var string */
    protected $feedSamplePath;

    protected function setUp(): void
    {
        Reader\Reader::reset();
        $this->feedSamplePath = __DIR__ . '/_files/Common';
    }

    /**
     * Check DOM Retrieval and Information Methods
     */
    public function testGetsDomDocumentObject(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertInstanceOf(DOMDocument::class, $feed->getDomDocument());
    }

    public function testGetsDomXpathObject(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertInstanceOf(DOMXPath::class, $feed->getXpath());
    }

    public function testGetsXpathPrefixString(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertEquals('/atom:feed', $feed->getXpathPrefix());
    }

    public function testGetsDomElementObject(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertInstanceOf(DOMElement::class, $feed->getElement());
    }

    public function testSaveXmlOutputsXmlStringForFeed(): void
    {
        $feed     = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $expected = file_get_contents($this->feedSamplePath . '/atom_rewrittenbydom.xml');
        $expected = str_replace("\r\n", "\n", $expected);
        $this->assertEquals($expected, $feed->saveXml());
    }

    public function testGetsNamedExtension(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertInstanceOf(Feed::class, $feed->getExtension('Atom'));
    }

    public function testReturnsNullIfExtensionDoesNotExist(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertEquals(null, $feed->getExtension('Foo'));
    }

    /**
     * @group Laminas-8213
     */
    public function testReturnsEncodingOfFeed(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    /**
     * @group Laminas-8213
     */
    public function testReturnsEncodingOfFeedAsUtf8IfUndefined(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom_noencodingdefined.xml')
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }
}
