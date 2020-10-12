<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Reader\Feed;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Laminas\Feed\Reader;
use Laminas\Feed\Reader\Extension\Atom\Feed;
use PHPUnit\Framework\TestCase;

/**
 * @group Laminas_Feed
 * @group Reader\Reader
 */
class CommonTest extends TestCase
{
    protected $feedSamplePath;

    protected function setUp(): void
    {
        Reader\Reader::reset();
        $this->feedSamplePath = dirname(__FILE__) . '/_files/Common';
    }

    /**
     * Check DOM Retrieval and Information Methods
     */
    public function testGetsDomDocumentObject()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertInstanceOf(DOMDocument::class, $feed->getDomDocument());
    }

    public function testGetsDomXpathObject()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertInstanceOf(DOMXPath::class, $feed->getXpath());
    }

    public function testGetsXpathPrefixString()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertEquals('/atom:feed', $feed->getXpathPrefix());
    }

    public function testGetsDomElementObject()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertInstanceOf(DOMElement::class, $feed->getElement());
    }

    public function testSaveXmlOutputsXmlStringForFeed()
    {
        $feed     = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $expected = file_get_contents($this->feedSamplePath . '/atom_rewrittenbydom.xml');
        $expected = str_replace("\r\n", "\n", $expected);
        $this->assertEquals($expected, $feed->saveXml());
    }

    public function testGetsNamedExtension()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertInstanceOf(Feed::class, $feed->getExtension('Atom'));
    }

    public function testReturnsNullIfExtensionDoesNotExist()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertEquals(null, $feed->getExtension('Foo'));
    }

    /**
     * @group Laminas-8213
     */
    public function testReturnsEncodingOfFeed()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom.xml')
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    /**
     * @group Laminas-8213
     */
    public function testReturnsEncodingOfFeedAsUtf8IfUndefined()
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/atom_noencodingdefined.xml')
        );
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }
}
