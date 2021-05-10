<?php

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
class RssTest extends TestCase
{
    protected $validWriter;
    protected $validEntry;

    protected function setUp(): void
    {
        Writer\Writer::reset();
        $this->validWriter = new Writer\Feed();

        $this->validWriter->setType('rss');

        $this->validWriter->setTitle('This is a test feed.');
        $this->validWriter->setDescription('This is a test description.');
        $this->validWriter->setLink('http://www.example.com');
        $this->validEntry = $this->validWriter->createEntry();
        $this->validEntry->setTitle('This is a test entry.');
        $this->validEntry->setDescription('This is a test entry description.');
        $this->validEntry->setLink('http://www.example.com/1');
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
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $renderer->render();
    }

    public function testEntryEncodingHasBeenSet(): void
    {
        $this->validWriter->setEncoding('iso-8859-1');
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('iso-8859-1', $entry->getEncoding());
    }

    public function testEntryEncodingDefaultIsUsedIfEncodingNotSetByHand(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('UTF-8', $entry->getEncoding());
    }

    public function testEntryTitleHasBeenSet(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('This is a test entry.', $entry->getTitle());
    }

    public function testEntryTitleIfMissingThrowsExceptionIfDescriptionAlsoMissing(): void
    {
        $atomFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->remove('title');
        $this->validEntry->remove('description');

        $this->expectException(ExceptionInterface::class);
        $atomFeed->render();
    }

    public function testEntryTitleCharDataEncoding(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->setTitle('<>&\'"áéíóú');
        $feed  = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('<>&\'"áéíóú', $entry->getTitle());
    }

    public function testEntrySummaryDescriptionHasBeenSet(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('This is a test entry description.', $entry->getDescription());
    }

    public function testEntryDescriptionIfMissingThrowsExceptionIfAlsoNoTitle(): void
    {
        $atomFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->remove('description');
        $this->validEntry->remove('title');

        $this->expectException(ExceptionInterface::class);
        $atomFeed->render();
    }

    public function testEntryDescriptionCharDataEncoding(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->setDescription('<>&\'"áéíóú');
        $feed  = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('<>&\'"áéíóú', $entry->getDescription());
    }

    public function testEntryContentHasBeenSet(): void
    {
        $this->validEntry->setContent('This is test entry content.');
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('This is test entry content.', $entry->getContent());
    }

    public function testEntryContentCharDataEncoding(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->setContent('<>&\'"áéíóú');
        $feed  = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('<>&\'"áéíóú', $entry->getContent());
    }

    public function testEntryUpdatedDateHasBeenSet(): void
    {
        $this->validEntry->setDateModified(1234567890);
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals(1234567890, $entry->getDateModified()->getTimestamp());
    }

    public function testEntryPublishedDateHasBeenSet(): void
    {
        $this->validEntry->setDateCreated(1234567000);
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals(1234567000, $entry->getDateCreated()->getTimestamp());
    }

    public function testEntryIncludesLinkToHtmlVersionOfFeed(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('http://www.example.com/1', $entry->getLink());
    }

    public function testEntryHoldsAnyAuthorAdded(): void
    {
        $this->validEntry->addAuthor([
            'name'  => 'Jane',
            'email' => 'jane@example.com',
            'uri'   => 'http://www.example.com/jane',
        ]);
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $author   = $entry->getAuthor();
        $this->assertEquals(['name' => 'Jane'], $entry->getAuthor());
    }

    public function testEntryAuthorCharDataEncoding(): void
    {
        $this->validEntry->addAuthor([
            'name'  => '<>&\'"áéíóú',
            'email' => 'jane@example.com',
            'uri'   => 'http://www.example.com/jane',
        ]);
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $author   = $entry->getAuthor();
        $this->assertEquals(['name' => '<>&\'"áéíóú'], $entry->getAuthor());
    }

    public function testEntryHoldsAnyEnclosureAdded(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
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

    public function testAddsEnclosureThrowsExceptionOnMissingType(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->setEnclosure([
            'uri'    => 'http://example.com/audio.mp3',
            'length' => '1337',
        ]);

        $this->expectException(ExceptionInterface::class);
        $renderer->render();
    }

    public function testAddsEnclosureThrowsExceptionOnMissingLength(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->setEnclosure([
            'type' => 'audio/mpeg',
            'uri'  => 'http://example.com/audio.mp3',
        ]);

        $this->expectException(ExceptionInterface::class);
        $renderer->render();
    }

    public function testAddsEnclosureThrowsExceptionOnNonNumericLength(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->setEnclosure([
            'type'   => 'audio/mpeg',
            'uri'    => 'http://example.com/audio.mp3',
            'length' => 'abc',
        ]);

        $this->expectException(ExceptionInterface::class);
        $renderer->render();
    }

    public function testAddsEnclosureThrowsExceptionOnNegativeLength(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->setEnclosure([
            'type'   => 'audio/mpeg',
            'uri'    => 'http://example.com/audio.mp3',
            'length' => -23,
        ]);

        $this->expectException(ExceptionInterface::class);
        $renderer->render();
    }

    /**
     * @doesNotPerformAssertions
     *
     */
    public function testEnclosureWorksWithZeroLength(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->setEnclosure([
            'type'   => 'audio/mpeg',
            'uri'    => 'http://example.com/audio.mp3',
            'length' => 0,
        ]);
        $renderer->render();
    }

    /**
     * @doesNotPerformAssertions
     *
     */
    public function testEnclosureWorksWithPositiveLength(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->setEnclosure([
            'type'   => 'audio/mpeg',
            'uri'    => 'http://example.com/audio.mp3',
            'length' => 23,
        ]);
        $renderer->render();
    }

    /**
     * @doesNotPerformAssertions
     *
     */
    public function testEnclosureWorksWithPositiveLengthString(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->setEnclosure([
            'type'   => 'audio/mpeg',
            'uri'    => 'http://example.com/audio.mp3',
            'length' => '23',
        ]);
        $renderer->render();
    }

    public function testEntryIdHasBeenSet(): void
    {
        $this->validEntry->setId('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6', $entry->getId());
    }

    public function testEntryIdHasBeenSetWithPermaLinkAsFalseWhenNotUri(): void
    {
        $this->markTestIncomplete('Untest due to LaminasR potential bug');
    }

    public function testEntryIdDefaultIsUsedIfNotSetByHand(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $this->assertEquals($entry->getLink(), $entry->getId());
    }

    public function testCommentLinkRendered(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->setCommentLink('http://www.example.com/id/1');
        $feed  = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals('http://www.example.com/id/1', $entry->getCommentLink());
    }

    public function testCommentCountRendered(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $this->validEntry->setCommentCount(22);
        $feed  = Reader\Reader::importString($renderer->render()->saveXml());
        $entry = $feed->current();
        $this->assertEquals(22, $entry->getCommentCount());
    }

    public function testCommentFeedLinksRendered(): void
    {
        $renderer = new Renderer\Feed\Rss($this->validWriter);
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
        // Skipped assertion is because RSS has no facility to show Atom feeds without an extension
        $this->assertEquals('http://www.example.com/rss/id/1', $entry->getCommentFeedLink('rss'));
        //$this->assertEquals('http://www.example.com/atom/id/1', $entry->getCommentFeedLink('atom'));
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
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $expected = [
            [
                'term'   => 'cat_dog',
                'label'  => 'cat_dog',
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

    /**
     * @group LaminasWCHARDATA01
     *
     */
    public function testCategoriesCharDataEncoding(): void
    {
        $this->validEntry->addCategories([
            [
                'term'   => '<>&\'"áéíóú',
                'label'  => 'Cats & Dogs',
                'scheme' => 'http://example.com/schema1',
            ],
            ['term' => 'cat_dog2'],
        ]);
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());
        $entry    = $feed->current();
        $expected = [
            [
                'term'   => '<>&\'"áéíóú',
                'label'  => '<>&\'"áéíóú',
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
        $renderer = new Renderer\Entry\Rss($this->validEntry);
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

    public function testPermalinkShouldBeEqualToLinkOfEntry(): void
    {
        // Render
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());

        /** @var Reader\Entry\Rss $entry */
        $entry = $feed->current();

        // Test
        $this->assertSame(
            $this->validEntry->getLink(),
            $entry->getPermalink()
        );
    }

    public function testPermalinkShouldBeNullOnNonAbsoluteUri(): void
    {
        // Update entry
        $this->validEntry->remove('link');
        $this->validEntry->setId('non-absolute-uri');

        // Render
        $renderer = new Renderer\Feed\Rss($this->validWriter);
        $feed     = Reader\Reader::importString($renderer->render()->saveXml());

        /** @var Reader\Entry\Rss $entry */
        $entry = $feed->current();

        // Test
        $this->assertNull($entry->getPermalink());
    }
}
