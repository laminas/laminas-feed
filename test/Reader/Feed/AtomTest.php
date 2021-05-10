<?php

namespace LaminasTest\Feed\Reader\Feed;

use DateTime;
use Laminas\Feed\Reader;
use PHPUnit\Framework\TestCase;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Reader
 */
class AtomTest extends TestCase
{
    protected $feedSamplePath;

    protected $options = [];

    protected $expectedCats = [];

    protected $expectedCatsDc = [];

    protected function setUp(): void
    {
        Reader\Reader::reset();
        $this->feedSamplePath = dirname(__FILE__) . '/_files/Atom';

        $this->expectedCats   = [
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
        $this->expectedCatsDc = [
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
    }

    /**
     * Get Title (Unencoded Text)
     *
     */
    public function testGetsTitleFromAtom03(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/atom03.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/title/plain/atom10.xml')
        );
        $this->assertEquals('My Title', $feed->getTitle());
    }

    public function testGetsTitleNullFromEmpty(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/empty.xml')
        );
        $this->assertNull($feed->getTitle());
    }

    /**
     * Get Authors (Unencoded Text)
     *
     */
    public function testGetsAuthorArrayFromAtom03(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom03.xml')
        );

        $authors = [
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs', 'uri' => 'http://www.example.com'],
            ['name' => 'Joe Bloggs', 'uri' => 'http://www.example.com'],
            ['name' => 'Joe Bloggs'],
            ['email' => 'joe@example.com', 'uri' => 'http://www.example.com'],
            ['uri' => 'http://www.example.com'],
            ['email' => 'joe@example.com'],
        ];

        $this->assertEquals($authors, (array) $feed->getAuthors());
    }

    public function testGetsAuthorArrayFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom10.xml')
        );

        $authors = [
            ['email' => 'joe@example.com', 'name' => 'Joe Bloggs', 'uri' => 'http://www.example.com'],
            ['name' => 'Joe Bloggs', 'uri' => 'http://www.example.com'],
            ['name' => 'Joe Bloggs'],
            ['email' => 'joe@example.com', 'uri' => 'http://www.example.com'],
            ['uri' => 'http://www.example.com'],
            ['email' => 'joe@example.com'],
        ];

        $this->assertEquals($authors, (array) $feed->getAuthors());
    }

    /**
     * Get Single Author (Unencoded Text)
     *
     */
    public function testGetsSingleAuthorFromAtom03(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom03.xml')
        );

        $this->assertEquals(
            ['name' => 'Joe Bloggs', 'email' => 'joe@example.com', 'uri' => 'http://www.example.com'],
            $feed->getAuthor()
        );
    }

    public function testGetsSingleAuthorFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/author/plain/atom10.xml')
        );

        $this->assertEquals(
            ['name' => 'Joe Bloggs', 'email' => 'joe@example.com', 'uri' => 'http://www.example.com'],
            $feed->getAuthor()
        );
    }

    public function testGetsSingleAuthorNullFromEmpty(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/empty.xml')
        );
        $this->assertNull($feed->getAuthor());
    }

    /**
     * Get creation date (Unencoded Text)
     *
     */
    public function testGetsDateCreatedFromAtom03(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/datecreated/plain/atom03.xml')
        );
        $edate = DateTime::createFromFormat(DateTime::ATOM, '2009-03-07T08:03:50Z');
        $this->assertEquals($edate, $feed->getDateCreated());
    }

    public function testGetsDateCreatedFromAtom10(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/datecreated/plain/atom10.xml')
        );
        $edate = DateTime::createFromFormat(DateTime::ATOM, '2009-03-07T08:03:50Z');
        $this->assertEquals($edate, $feed->getDateCreated());
    }

    public function testGetsDateCreatedNullFromEmpty(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/empty.xml')
        );
        $this->assertNull($feed->getDateCreated());
    }

    /**
     * Get modification date (Unencoded Text)
     *
     */
    public function testGetsDateModifiedFromAtom03(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/datemodified/plain/atom03.xml')
        );
        $edate = DateTime::createFromFormat(DateTime::ATOM, '2009-03-07T08:03:50Z');
        $this->assertEquals($edate, $feed->getDateModified());
    }

    public function testGetsDateModifiedFromAtom10(): void
    {
        $feed  = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/datemodified/plain/atom10.xml')
        );
        $edate = DateTime::createFromFormat(DateTime::ATOM, '2009-03-07T08:03:50Z');
        $this->assertEquals($edate, $feed->getDateModified());
    }

    public function testGetsDateModifiedNullFromEmpty(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/empty.xml')
        );
        $this->assertNull($feed->getDateModified());
    }

    /**
     * Get Last Build Date (Unencoded Text)
     *
     */
    public function testGetsLastBuildDateAlwaysReturnsNullForAtom(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/datemodified/plain/atom10.xml')
        );
        $this->assertNull($feed->getLastBuildDate());
    }

    /**
     * Get Generator (Unencoded Text)
     *
     */
    public function testGetsGeneratorFromAtom03(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/atom03.xml')
        );
        $this->assertEquals('Laminas_Feed', $feed->getGenerator());
    }

    public function testGetsGeneratorFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/generator/plain/atom10.xml')
        );
        $this->assertEquals('Laminas_Feed', $feed->getGenerator());
    }

    public function testGetsGeneratorNullFromEmpty(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/empty.xml')
        );
        $this->assertNull($feed->getGenerator());
    }

    /**
     * Get Copyright (Unencoded Text)
     *
     */
    public function testGetsCopyrightFromAtom03(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/atom03.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/copyright/plain/atom10.xml')
        );
        $this->assertEquals('Copyright 2008', $feed->getCopyright());
    }

    public function testGetsCopyrightNullFromEmpty(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/empty.xml')
        );
        $this->assertNull($feed->getCopyright());
    }

    /**
     * Get Description (Unencoded Text)
     *
     */
    public function testGetsDescriptionFromAtom03(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/atom03.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/description/plain/atom10.xml')
        );
        $this->assertEquals('My Description', $feed->getDescription());
    }

    public function testGetsDescriptionNullFromEmpty(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/empty.xml')
        );
        $this->assertNull($feed->getDescription());
    }

    /**
     * Get Id (Unencoded Text)
     *
     */
    public function testGetsIdFromAtom03(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/id/plain/atom03.xml')
        );
        $this->assertEquals('123', $feed->getId());
    }

    public function testGetsIdFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/id/plain/atom10.xml')
        );
        $this->assertEquals('123', $feed->getId());
    }

    public function testGetsIdNullFromEmpty(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/empty.xml')
        );
        $this->assertNull($feed->getId());
    }

    /**
     * Get Language (Unencoded Text)
     *
     */
    public function testGetsLanguageFromAtom03(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/atom03.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/language/plain/atom10.xml')
        );
        $this->assertEquals('en-GB', $feed->getLanguage());
    }

    public function testGetsLanguageNullFromEmpty(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/empty.xml')
        );
        $this->assertNull($feed->getLanguage());
    }

    /**
     * Get Link (Unencoded Text)
     *
     */
    public function testGetsLinkFromAtom03(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/atom03.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/atom10.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromAtom10WithNoRelAttribute(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/atom10-norel.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkFromAtom10WithRelativeUrl(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/atom10-relative.xml')
        );
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testGetsLinkNullFromEmpty(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/empty.xml')
        );
        $this->assertNull($feed->getLink());
    }

    /**
     * Get Base Uri
     *
     */
    public function testGetsBaseUriFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/atom10-relative.xml')
        );
        $this->assertEquals('http://www.example.com/', $feed->getBaseUrl());
    }

    /**
     * Get Feed Link (Unencoded Text)
     *
     */
    public function testGetsFeedLinkFromAtom03(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/atom03.xml')
        );
        $this->assertEquals('http://www.example.com/feed/atom', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/atom10.xml')
        );
        $this->assertEquals('http://www.example.com/feed/atom', $feed->getFeedLink());
    }

    public function testGetsFeedLinkFromAtom10IfRelativeUri(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/atom10-relative.xml')
        );
        $this->assertEquals('http://www.example.com/feed/atom', $feed->getFeedLink());
    }

    public function testGetsOriginalSourceUriIfFeedLinkNotAvailableFromFeed(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/feedlink/plain/atom10_NoFeedLink.xml')
        );
        $feed->setOriginalSourceUri('http://www.example.com/feed/atom');
        $this->assertEquals('http://www.example.com/feed/atom', $feed->getFeedLink());
    }

    public function testGetsFeedLinkNullFromEmpty(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/empty.xml')
        );
        $this->assertNull($feed->getFeedLink());
    }

    /**
     * Get Pubsubhubbub Hubs
     *
     */
    public function testGetsHubsFromAtom03(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/atom03.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    public function testGetsHubsFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/hubs/plain/atom10.xml')
        );
        $this->assertEquals([
            'http://www.example.com/hub1',
            'http://www.example.com/hub2',
        ], $feed->getHubs());
    }

    /**
     * Implements Countable
     *
     */
    public function testCountableInterface(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/link/plain/atom10.xml')
        );
        $this->assertCount(0, $feed);
    }

    /**
     * Get category data
     *
     */
    public function testGetsCategoriesFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/atom10.xml')
        );
        $this->assertEquals($this->expectedCats, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromAtom03Atom10Extension(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/atom03.xml')
        );
        $this->assertEquals($this->expectedCats, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'Cat & Dog'], array_values($feed->getCategories()->getValues()));
    }

    // DC 1.0/1.1 for Atom 0.3

    public function testGetsCategoriesFromAtom03Dc10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc10/atom03.xml')
        );
        $this->assertEquals($this->expectedCatsDc, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromAtom03Dc11(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/dc11/atom03.xml')
        );
        $this->assertEquals($this->expectedCatsDc, (array) $feed->getCategories());
        $this->assertEquals(['topic1', 'topic2'], array_values($feed->getCategories()->getValues()));
    }

    // No Categories In Entry

    public function testGetsCategoriesFromAtom10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/none/atom10.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    public function testGetsCategoriesFromAtom03None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/category/plain/none/atom03.xml')
        );
        $this->assertEquals([], (array) $feed->getCategories());
        $this->assertEquals([], array_values($feed->getCategories()->getValues()));
    }

    /**
     * Get Image (Unencoded Text)
     *
     */
    public function testGetsImageFromAtom03(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/atom03.xml')
        );
        $this->assertEquals(['uri' => 'http://www.example.com/logo.gif'], $feed->getImage());
    }

    public function testGetsImageFromAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/atom10.xml')
        );
        $this->assertEquals(['uri' => 'http://www.example.com/logo.gif'], $feed->getImage());
    }

    /**
     * Get Image (Unencoded Text) When Missing
     *
     */
    public function testGetsImageFromAtom03None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/none/atom03.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }

    public function testGetsImageFromAtom10None(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/image/plain/none/atom10.xml')
        );
        $this->assertEquals(null, $feed->getImage());
    }
}
