<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Reader\Feed;

use DateTime;
use DateTimeInterface;
use Laminas\Feed\Reader;
use PHPUnit\Framework\TestCase;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Reader
 */
class RssTest extends TestCase
{
    /** @var string */
    protected $feedSamplePath;

    protected $expectedCats = [];

    protected $expectedCatsRdf = [];

    protected $expectedCatsAtom = [];

    protected function setUp(): void
    {
        Reader\Reader::reset();
        $this->feedSamplePath = dirname(__FILE__) . '/_files/Rss';

        $this->expectedCats     = [
            [
                'term'   => 'topic1',
                'scheme' => 'http://example.com/schema1',
                'label'  => 'topic1',
            ],
            [
                'term'   => 'topic1',
                'scheme' => 'http://example.com/schema2',
                'label'  => 'topic1',
            ],
            [
                'term'   => 'topic2',
                'scheme' => 'http://example.com/schema1',
                'label'  => 'topic2',
            ],
        ];
        $this->expectedCatsRdf  = [
            [
                'term'   => 'topic1',
                'scheme' => null,
                'label'  => 'topic1',
            ],
            [
                'term'   => 'topic2',
                'scheme' => null,
                'label'  => 'topic2',
            ],
        ];
        $this->expectedCatsAtom = [
            [
                'term'   => 'topic1',
                'scheme' => 'http://example.com/schema1',
                'label'  => 'topic1',
            ],
            [
                'term'   => 'topic1',
                'scheme' => 'http://example.com/schema2',
                'label'  => 'topic1',
            ],
            [
                'term'   => 'cat_dog',
                'scheme' => 'http://example.com/schema1',
                'label'  => 'Cat & Dog',
            ],
        ];
    }

    /**
     * Get Title (Unencoded Text)
     *
     */
    public function testGetsTitleFromRss20(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // DC 1.0

    public function testGetsTitleFromRss20Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc10/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc10/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc10/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc10/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc10/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc10/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc10/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // DC 1.1

    public function testGetsTitleFromRss20Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc11/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc11/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc11/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc11/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc11/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc11/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/dc11/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // Atom 1.0

    public function testGetsTitleFromRss20Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/atom10/rss20.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss094Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/atom10/rss094.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss093Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/atom10/rss093.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss092Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/atom10/rss092.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss091Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/atom10/rss091.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss10Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/atom10/rss10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromRss090Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/atom10/rss090.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    // Missing Title

    public function testGetsTitleFromRss20None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss094None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss093None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss092None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss091None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    public function testGetsTitleFromRss090None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getTitle());
    }

    /**
     * Get Authors (Unencoded Text)
     *
     */
    public function testGetsAuthorArrayFromRss20(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss20.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss094(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss094.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss093(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss093.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss092(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss092.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss091(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss091.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss10.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss090(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss090.xml')
        );
        $this->assertEquals([
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs'],
            ['email' => 'jane@example.com', 'name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    // DC 1.0

    public function testGetsAuthorArrayFromRss20Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss20.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss094Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss094.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss093Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss093.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss092Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss092.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss091Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss091.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss10Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss10.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss090Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss090.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    // DC 1.1

    public function testGetsAuthorArrayFromRss20Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss20.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss094Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss094.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss093Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss093.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss092Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss092.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss091Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss091.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss10Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss10.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss090Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss090.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    // Atom 1.0

    public function testGetsAuthorArrayFromRss20Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom10/rss20.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss094Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom10/rss094.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss093Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom10/rss093.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss092Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom10/rss092.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss091Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom10/rss091.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss10Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom10/rss10.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    public function testGetsAuthorArrayFromRss090Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom10/rss090.xml')
        );
        $this->assertEquals([
            ['name' => 'Joe Bloggs'],
            ['name' => 'Jane Bloggs'],
        ], (array) $feed->getAuthors());
        $this->assertEquals(['Joe Bloggs', 'Jane Bloggs'], $feed->getAuthors()->getValues());
    }

    // Missing Authors

    public function testGetsAuthorArrayFromRss20None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss094None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss093None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss092None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss091None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromRss090None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getAuthors());
    }

    /**
     * Get Single Author (Unencoded Text)
     *
     */
    public function testGetsSingleAuthorFromRss20(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss20.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss094.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss093.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss092.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss091.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss10.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/rss090.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs', 'email' => 'joe@example.com'], $feed->getAuthor());
    }

    // DC 1.0

    public function testGetsSingleAuthorFromRss20Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss20.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss094.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss093.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss092.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss091.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss10.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc10/rss090.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    // DC 1.1

    public function testGetsSingleAuthorFromRss20Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss20.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss094.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss093.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss092.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss091.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss10.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/dc11/rss090.xml')
        );
        $this->assertEquals(['name' => 'Joe Bloggs'], $feed->getAuthor());
    }

    // Missing Author

    public function testGetsSingleAuthorFromRss20None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss094None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss093None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss092None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss091None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    public function testGetsSingleAuthorFromRss090None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getAuthor());
    }

    /**
     * Get Copyright (Unencoded Text)
     *
     */
    public function testGetsCopyrightFromRss20(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/rss20.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/rss094.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/rss093.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/rss092.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/rss091.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/rss10.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/rss090.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    // DC 1.0

    public function testGetsCopyrightFromRss20Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc10/rss20.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc10/rss094.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc10/rss093.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc10/rss092.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc10/rss091.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc10/rss10.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc10/rss090.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    // DC 1.1

    public function testGetsCopyrightFromRss20Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc11/rss20.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc11/rss094.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc11/rss093.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc11/rss092.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc11/rss091.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc11/rss10.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/dc11/rss090.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    // Missing Copyright

    public function testGetsCopyrightFromRss20None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss094None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss093None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss092None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss091None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    public function testGetsCopyrightFromRss090None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getCopyright());
    }

    /**
     * Get Description (Unencoded Text)
     *
     */
    public function testGetsDescriptionFromRss20(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/rss20.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/rss094.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/rss093.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/rss092.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/rss091.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/rss10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/rss090.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    // DC 1.0

    public function testGetsDescriptionFromRss20Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc10/rss20.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc10/rss094.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc10/rss093.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc10/rss092.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc10/rss091.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc10/rss10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc10/rss090.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    // DC 1.1

    public function testGetsDescriptionFromRss20Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc11/rss20.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc11/rss094.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc11/rss093.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc11/rss092.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc11/rss091.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc11/rss10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/dc11/rss090.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    // Missing Description

    public function testGetsDescriptionFromRss20None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss094None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss093None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss092None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss091None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    public function testGetsDescriptionFromRss090None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getDescription());
    }

    /**
     * Get Language (Unencoded Text)
     *
     */
    public function testGetsLanguageFromRss20(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/rss20.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/rss094.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/rss093.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/rss092.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/rss091.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/rss10.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/rss090.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    // DC 1.0

    public function testGetsLanguageFromRss20Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc10/rss20.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc10/rss094.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc10/rss093.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc10/rss092.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc10/rss091.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc10/rss10.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc10/rss090.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    // DC 1.1

    public function testGetsLanguageFromRss20Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc11/rss20.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc11/rss094.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc11/rss093.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc11/rss092.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc11/rss091.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc11/rss10.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/dc11/rss090.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    // Other

    public function testGetsLanguageFromRss10XmlLang(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/rdf/rss10.xml')
        );
        $this->assertEquals('en', $feed->getLanguage());
    }

    // Missing Language

    public function testGetsLanguageFromRss20None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss094None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss093None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss092None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss091None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testGetsLanguageFromRss090None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getLanguage());
    }

    /**
     * Get Link (Unencoded Text)
     *
     */
    public function testGetsLinkFromRss20(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/rss20.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss094(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/rss094.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss093(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/rss093.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss092(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/rss092.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss091(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/rss091.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/rss10.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromRss090(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/rss090.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    // Missing Link

    public function testGetsLinkFromRss20None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss094None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss093None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss092None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss091None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    public function testGetsLinkFromRss090None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getLink());
    }

    /**
     * Implements Countable
     *
     */
    public function testCountableInterface(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/none/rss090.xml')
        );
        $this->assertCount(0, $feed);
    }

    /**
     * Get Feed Link (Unencoded Text)
     *
     */
    public function testGetsFeedLinkFromRss20(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/rss20.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsOriginalSourceUriIfFeedLinkNotAvailableFromFeed(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/rss20_NoFeedLink.xml')
        );
        $feed->setOriginalSourceUri('http://www.example.com/feed/rss');
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss094(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/rss094.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss093(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/rss093.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss092(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/rss092.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss091(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/rss091.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/rss10.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss090(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/rss090.xml')
        );
        $this->assertEquals('http://www.example.com/feed/rss', $feed->getFeedLink());
    }

    // Missing Feed Link

    public function testGetsFeedLinkFromRss20None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss094None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss093None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss092None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss091None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromRss090None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getFeedLink());
    }

    /**
     * Get Generator (Unencoded Text)
     *
     */
    public function testGetsGeneratorFromRss20(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/rss20.xml')
        );
        $this->assertEquals('Laminas_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss094(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/rss094.xml')
        );
        $this->assertEquals('Laminas_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss093(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/rss093.xml')
        );
        $this->assertEquals('Laminas_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss092(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/rss092.xml')
        );
        $this->assertEquals('Laminas_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss091(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/rss091.xml')
        );
        $this->assertEquals('Laminas_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/rss10.xml')
        );
        $this->assertEquals('Laminas_Feed_Writer', $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss090(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/rss090.xml')
        );
        $this->assertEquals('Laminas_Feed_Writer', $feed->getGenerator());
    }

    // Missing Generator

    public function testGetsGeneratorFromRss20None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss094None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss093None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss092None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss091None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    public function testGetsGeneratorFromRss090None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getGenerator());
    }

    /**
     * Get Last Build Date (Unencoded Text)
     *
     */
    public function testGetsLastBuildDateFromRss20(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/lastbuilddate/plain/rss20.xml')
        );
        $edate = DateTime::createFromFormat(DateTime::ISO8601, '2009-03-07T08:03:50Z');
        $this->assertEquals($edate, $feed->getLastBuildDate());
    }

    public function testGetsLastBuildDateFromRss20None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/lastbuilddate/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getLastBuildDate());
    }

    /**
     * Get DateModified (Unencoded Text)
     *
     * @param string $path
     * @param DateTimeInterface|null $edate
     * @dataProvider dateModifiedProvider
     */
    public function testGetsDateModified($path, $edate): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . $path)
        );

        $this->assertEquals($edate, $feed->getDateModified());
    }

    /**
     * @psalm-return array<int,array{0:string,1:DateTimeInterface|null}>
     */
    public function dateModifiedProvider(): array
    {
        $iso = DateTime::createFromFormat(DateTimeInterface::ISO8601, '2009-03-07T08:03:50Z');
        assert($iso instanceof DateTimeInterface);
        $us  = DateTime::createFromFormat(DateTimeInterface::ISO8601, '2010-01-04T02:14:00-0600');
        assert($us instanceof DateTimeInterface);

        return [
            ['/datemodified/plain/rss20.xml', $iso],
            ['/datemodified/plain/rss20_en_US.xml', $us],
            ['/datemodified/plain/dc10/rss20.xml', $iso],
            ['/datemodified/plain/dc10/rss094.xml', $iso],
            ['/datemodified/plain/dc10/rss093.xml', $iso],
            ['/datemodified/plain/dc10/rss092.xml', $iso],
            ['/datemodified/plain/dc10/rss091.xml', $iso],
            ['/datemodified/plain/dc10/rss10.xml', $iso],
            ['/datemodified/plain/dc10/rss090.xml', $iso],
            ['/datemodified/plain/dc11/rss20.xml', $iso],
            ['/datemodified/plain/dc11/rss094.xml', $iso],
            ['/datemodified/plain/dc11/rss093.xml', $iso],
            ['/datemodified/plain/dc11/rss092.xml', $iso],
            ['/datemodified/plain/dc11/rss091.xml', $iso],
            ['/datemodified/plain/dc11/rss10.xml', $iso],
            ['/datemodified/plain/dc11/rss090.xml', $iso],
            ['/datemodified/plain/atom10/rss20.xml', $iso],
            ['/datemodified/plain/atom10/rss094.xml', $iso],
            ['/datemodified/plain/atom10/rss093.xml', $iso],
            ['/datemodified/plain/atom10/rss092.xml', $iso],
            ['/datemodified/plain/atom10/rss091.xml', $iso],
            ['/datemodified/plain/atom10/rss10.xml', $iso],
            ['/datemodified/plain/atom10/rss090.xml', $iso],
            ['/datemodified/plain/none/rss20.xml', null],
            ['/datemodified/plain/none/rss094.xml', null],
            ['/datemodified/plain/none/rss093.xml', null],
            ['/datemodified/plain/none/rss092.xml', null],
            ['/datemodified/plain/none/rss091.xml', null],
            ['/datemodified/plain/none/rss10.xml', null],
            ['/datemodified/plain/none/rss090.xml', null],
        ];
    }

    public function testGetDateModifiedShouldThrowExceptionForInvalidDate(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents(
                $this->feedSamplePath . '/datemodified/plain/invalid.xml'
            )
        );

        $this->expectException(Reader\Exception\RuntimeException::class);
        $this->expectExceptionMessage(
            'Could not load date due to unrecognised format (should follow RFC 822 or 2822).'
        );

        $feed->getDateModified();
    }

    /**
     * Get Hubs (Unencoded Text)
     *
     */
    public function testGetsHubsFromRss20(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/atom10/rss20.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    public function testGetsHubsFromRss094(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/atom10/rss094.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    public function testGetsHubsFromRss093(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/atom10/rss093.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    public function testGetsHubsFromRss092(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/atom10/rss092.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    public function testGetsHubsFromRss091(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/atom10/rss091.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    public function testGetsHubsFromRss10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/atom10/rss10.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    public function testGetsHubsFromRss090(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/atom10/rss090.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    // Missing Hubs

    public function testGetsHubsFromRss20None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss094None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss093None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss092None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss091None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    public function testGetsHubsFromRss090None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getHubs());
    }

    /**
     * Get category data
     *
     */
    public function testGetsCategoriesFromRss20(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/rss20.xml')
        );
        $this->assertEquals($this->expectedCats, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    // DC 1.0

    public function testGetsCategoriesFromRss090Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc10/rss090.xml')
        );
        $this->assertEquals($this->expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss091Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc10/rss091.xml')
        );
        $this->assertEquals($this->expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss092Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc10/rss092.xml')
        );
        $this->assertEquals($this->expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss093Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc10/rss093.xml')
        );
        $this->assertEquals($this->expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss094Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc10/rss094.xml')
        );
        $this->assertEquals($this->expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss10Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc10/rss10.xml')
        );
        $this->assertEquals($this->expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    // DC 1.1

    public function testGetsCategoriesFromRss090Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc11/rss090.xml')
        );
        $this->assertEquals($this->expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss091Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc11/rss091.xml')
        );
        $this->assertEquals($this->expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss092Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc11/rss092.xml')
        );
        $this->assertEquals($this->expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss093Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc11/rss093.xml')
        );
        $this->assertEquals($this->expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss094Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc11/rss094.xml')
        );
        $this->assertEquals($this->expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss10Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc11/rss10.xml')
        );
        $this->assertEquals($this->expectedCatsRdf, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    // Atom 1.0

    public function testGetsCategoriesFromRss090Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/atom10/rss090.xml')
        );
        $this->assertEquals($this->expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss091Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/atom10/rss091.xml')
        );
        $this->assertEquals($this->expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss092Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/atom10/rss092.xml')
        );
        $this->assertEquals($this->expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss093Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/atom10/rss093.xml')
        );
        $this->assertEquals($this->expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss094Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/atom10/rss094.xml')
        );
        $this->assertEquals($this->expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss10Atom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/atom10/rss10.xml')
        );
        $this->assertEquals($this->expectedCatsAtom, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    // No Categories In Entry

    public function testGetsCategoriesFromRss20None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/none/rss20.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss090None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/none/rss090.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss091None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/none/rss091.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss092None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/none/rss092.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss093None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/none/rss093.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss094None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/none/rss094.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromRss10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/none/rss10.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    /**
     * Get Image data (Unencoded Text)
     *
     */
    public function testGetsImageFromRss20(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/rss20.xml')
        );
        $this->assertEquals([
            'uri'         => 'http://www.example.com/image.gif',
            'link'        => 'http://www.example.com',
            'title'       => 'Image title',
            'height'      => '55',
            'width'       => '50',
            'description' => 'Image description',
        ], $feed->getImage());
    }

    public function testGetsImageFromRss094(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/rss094.xml')
        );
        $this->assertEquals([
            'uri'         => 'http://www.example.com/image.gif',
            'link'        => 'http://www.example.com',
            'title'       => 'Image title',
            'height'      => '55',
            'width'       => '50',
            'description' => 'Image description',
        ], $feed->getImage());
    }

    public function testGetsImageFromRss093(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/rss093.xml')
        );
        $this->assertEquals([
            'uri'         => 'http://www.example.com/image.gif',
            'link'        => 'http://www.example.com',
            'title'       => 'Image title',
            'height'      => '55',
            'width'       => '50',
            'description' => 'Image description',
        ], $feed->getImage());
    }

    public function testGetsImageFromRss092(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/rss092.xml')
        );
        $this->assertEquals([
            'uri'         => 'http://www.example.com/image.gif',
            'link'        => 'http://www.example.com',
            'title'       => 'Image title',
            'height'      => '55',
            'width'       => '50',
            'description' => 'Image description',
        ], $feed->getImage());
    }

    public function testGetsImageFromRss091(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/rss091.xml')
        );
        $this->assertEquals([
            'uri'         => 'http://www.example.com/image.gif',
            'link'        => 'http://www.example.com',
            'title'       => 'Image title',
            'height'      => '55',
            'width'       => '50',
            'description' => 'Image description',
        ], $feed->getImage());
    }

    /*public function testGetsImageFromRss10(): void
 {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss10.xml')
        );
        $this->assertEquals(array(
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description'
        ), $feed->getImage());
    }

    public function testGetsImageFromRss090(): void
 {
        $feed = Reader\Reader::importString(
            file_get_contents($this->_feedSamplePath.'/image/plain/rss090.xml')
        );
        $this->assertEquals(array(
            'uri' => 'http://www.example.com/image.gif',
            'link' => 'http://www.example.com',
            'title' => 'Image title',
            'height' => '55',
            'width' => '50',
            'description' => 'Image description'
        ), $feed->getImage());
    }*/

    /**
     * Get Image data (Unencoded Text) Missing
     *
     */
    public function testGetsImageFromRss20None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/none/rss20.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss094None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/none/rss094.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss093None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/none/rss093.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss092None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/none/rss092.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss091None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/none/rss091.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/none/rss10.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromRss090None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/none/rss090.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }
}
