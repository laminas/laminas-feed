<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Writer\Renderer\Entry;

use Laminas\Feed\Reader;
use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Exception\ExceptionInterface;
use Laminas\Feed\Writer\Renderer;
use LaminasTest\Feed\Writer\TestAsset;
use PHPUnit\Framework\TestCase;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Writer
 */
class AtomTest extends TestCase
{
    protected $validWriter;
    protected $validEntry;

    protected function setUp(): void
    {
        Writer\Writer::reset();
        $this->validWriter = new Writer\Feed();

        $this->validWriter->setType('atom');

        $this->validWriter->setTitle('This is a test feed.');
        $this->validWriter->setDescription('This is a test description.');
        $this->validWriter->setDateModified(1234567890);
        $this->validWriter->setLink('http://www.example.com');
        $this->validWriter->setFeedLink('http://www.example.com/atom', 'atom');
        $this->validWriter->addAuthor([
            'name'  => 'Joe',
            'email' => 'joe@example.com',
            'uri'   => 'http://www.example.com/joe',
        ]);
        $this->validEntry = $this->validWriter->createEntry();
        $this->validEntry->setTitle('This is a test entry.');
        $this->validEntry->setDescription('This is a test entry description.');
        $this->validEntry->setDateModified(1234567890);
        $this->validEntry->setDateCreated(1234567000);
        $this->validEntry->setLink('http://www.example.com/1');
        $this->validEntry->addAuthor([
            'name'  => 'Jane',
            'email' => 'jane@example.com',
            'uri'   => 'http://www.example.com/jane',
        ]);
        $this->validEntry->setContent('<p class="xhtml:">This is test content for <em>xhtml:</em></p>');
        $this->validWriter->addEntry($this->validEntry);
    }

    protected function tearDown(): void
    {
        Writer\Writer::reset();
        $this->validWriter = null;
        $this->validEntry  = null;
    }

    /**
     * @doesNotPerformAssertions
     *
     */
    public function testRenderMethodRunsMinimalWriterContainerProperlyBeforeICheckAtomCompliance(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $renderer->render();
    }

    public function testEntryEncodingHasBeenSet(): void
    {
        $this->validWriter->setEncoding('iso-8859-1');
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('iso-8859-1', $entry->getEncoding());
    }

    public function testEntryEncodingDefaultIsUsedIfEncodingNotSetByHand(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }

    public function testEntryTitleHasBeenSet(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('This is a test entry.', $entry->getTitle());
    }

    public function testFeedTitleIfMissingThrowsException(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->remove('title');

        $this->expectException(ExceptionInterface::class);
        $atomFeed->render();
    }

    public function testEntrySummaryDescriptionHasBeenSet(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('This is a test entry description.', $entry->getDescription());
    }

    /**
     * @group LaminasWATOMCONTENT
     */
    public function testEntryContentHasBeenSetXhtml(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('<p class="xhtml:">This is test content for <em>xhtml:</em></p>', $entry->getContent());
    }

    public function testFeedContentIfMissingThrowsExceptionIfThereIsNoLink(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->remove('content');
        $this->validEntry->remove('link');

        $this->expectException(ExceptionInterface::class);
        $atomFeed->render();
    }

    public function testEntryUpdatedDateHasBeenSet(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals(1234567890, $entry->getDateModified()->getTimestamp());
    }

    public function testFeedUpdatedDateIfMissingThrowsException(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->remove('dateModified');

        $this->expectException(ExceptionInterface::class);
        $atomFeed->render();
    }

    public function testEntryPublishedDateHasBeenSet(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals(1234567000, $entry->getDateCreated()->getTimestamp());
    }

    public function testEntryIncludesLinkToHtmlVersionOfFeed(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getLink());
    }

    public function testEntryHoldsAnyAuthorAdded(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $author   = $entry->getAuthor();
        $this->assertEquals([
            'name'  => 'Jane',
            'email' => 'jane@example.com',
            'uri'   => 'http://www.example.com/jane',
        ], $entry->getAuthor());
    }

    public function testEntryHoldsAnyEnclosureAdded(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->setEnclosure([
            'type'   => 'audio/mpeg',
            'length' => '1337',
            'uri'    => 'http://example.com/audio.mp3',
        ]);
        $feed  = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $enc   = $entry->getEnclosure();
        $this->assertEquals('audio/mpeg', $enc->type);
        $this->assertEquals('1337', $enc->length);
        $this->assertEquals('http://example.com/audio.mp3', $enc->url);
    }

    public function testEntryIdHasBeenSet(): void
    {
        $this->validEntry->setId('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6', $entry->getId());
    }

    public function testEntryIdHasBeenSetUsingSimpleTagUri(): void
    {
        $this->validEntry->setId('tag:example.org,2010:/foo/bar/');
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('tag:example.org,2010:/foo/bar/', $entry->getId());
    }

    public function testEntryIdHasBeenSetUsingComplexTagUri(): void
    {
        $this->validEntry->setId('tag:diveintomark.org,2004-05-27:/archives/2004/05/27/howto-atom-linkblog');
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals(
            'tag:diveintomark.org,2004-05-27:/archives/2004/05/27/howto-atom-linkblog',
            $entry->getId()
        );
    }

    public function testFeedIdDefaultIsUsedIfNotSetByHand(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals($entry->getLink(), $entry->getId());
    }

    public function testFeedIdIfMissingThrowsException(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->remove('id');
        $this->validEntry->remove('link');

        $this->expectException(ExceptionInterface::class);
        $atomFeed->render();
    }

    public function testFeedIdThrowsExceptionIfNotUri(): void
    {
        $this->markTestIncomplete('Pending Laminas\URI fix for validation');

        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->remove('id');
        $this->validEntry->remove('link');
        $this->validEntry->setId('not-a-uri');

        $this->expectException(ExceptionInterface::class);
        $atomFeed->render();
    }

    public function testCommentLinkRendered(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->setCommentLink('http://www.example.com/id/1');
        $feed  = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/id/1', $entry->getCommentLink());
    }

    public function testCommentCountRendered(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->setCommentCount(22);
        $feed  = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals(22, $entry->getCommentCount());
    }

    public function testCategoriesCanBeSet(): void
    {
        $this->validEntry->addCategories([
            [
                'term'   => 'cat_dog',
                'label'  => 'Cats & Dogs',
                'scheme' => 'http://example.com/schema1',
            ],
            ['term' => 'cat_dog2'],
        ]);
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed     = Reader\Reader::importString($atomFeed->saveXml());
        $entry    = $feed->current();
        $expected = [
            [
                'term'   => 'cat_dog',
                'label'  => 'Cats & Dogs',
                'scheme' => 'http://example.com/schema1',
            ],
            [
                'term'   => 'cat_dog2',
                'label'  => 'cat_dog2',
                'scheme' => null,
            ],
        ];
        $this->assertEquals($expected, (array) $entry->getCategories());
    }

    public function testCommentFeedLinksRendered(): void
    {
        $renderer = new Renderer\Feed\Atom($this->validWriter);
        $this->validEntry->setCommentFeedLinks([
            [
                'uri'  => 'http://www.example.com/atom/id/1',
                'type' => 'atom',
            ],
            [
                'uri'  => 'http://www.example.com/rss/id/1',
                'type' => 'rss',
            ],
        ]);
        $feed  = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        // Skipped over due to LaminasR bug (detects Atom in error when RSS requested)
        //$this->assertEquals('http://www.example.com/rss/id/1', $entry->getCommentFeedLink('rss'));
        $this->assertEquals('http://www.example.com/atom/id/1', $entry->getCommentFeedLink('atom'));
    }

    public function testEntryRendererEmitsNoticeDuringInstantiationWhenGooglePlayPodcastExtensionUnavailable(): void
    {
        // Since we create feed and entry writer instances in the test constructor,
        // we need to reset it _now_ before creating a new renderer.
        Writer\Writer::reset();
        Writer\Writer::setExtensionManager(new TestAsset\CustomExtensionManager());

        $notices = (object) [
            'messages' => [],
        ];

        set_error_handler(static function ($errno, $errstr) use ($notices) {
            $notices->messages[] = $errstr;
        }, \E_USER_NOTICE);
        $renderer = new Renderer\Entry\Atom($this->validEntry);
        restore_error_handler();

        $message = array_reduce($notices->messages, static function ($toReturn, $message) {
            if ('' !== $toReturn) {
                return $toReturn;
            }
            return false === strstr($message, 'GooglePlayPodcast') ? '' : $message;
        }, '');

        $this->assertNotEmpty(
            $message,
            'GooglePlayPodcast extension was present in extension manager, but was not expected to be'
        );
    }
}
