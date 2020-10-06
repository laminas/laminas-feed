<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Writer\Renderer\Feed;

use DateTime;
use DateTimeZone;
use Laminas\Feed\Reader;
use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Exception\ExceptionInterface;
use Laminas\Feed\Writer\Feed;
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

    protected function setUp(): void
    {
        Writer\Writer::reset();
        $this->validWriter = new Writer\Feed();
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

        $this->validWriter->setType('atom');
    }

    protected function tearDown(): void
    {
        Writer\Writer::reset();
        $this->validWriter = null;
    }

    public function testSetsWriterInConstructor(): void
    {
        $writer = new Writer\Feed();
        $feed   = new Renderer\Feed\Atom($writer);
        $this->assertInstanceOf(Feed::class, $feed->getDataContainer());
    }

    /**
     * @doesNotPerformAssertions
     *
     */
    public function testBuildMethodRunsMinimalWriterContainerProperlyBeforeICheckAtomCompliance(): void
    {
        $feed = new Renderer\Feed\Atom($this->validWriter);
        $feed->render();
    }

    public function testFeedEncodingHasBeenSet(): void
    {
        $this->validWriter->setEncoding('iso-8859-1');
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('iso-8859-1', $feed->getEncoding());
    }

    /**
     * @group 6358
     * @group 6935
     *
     */
    public function testDateModifiedHasTheCorrectFormat(): void
    {
        $date = new DateTime();
        $date->setTimestamp(1071336602);
        $date->setTimezone(new DateTimeZone('GMT'));
        $this->validWriter->setDateModified($date);
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $this->assertEquals(
            '2003-12-13T17:30:02+00:00',
            $atomFeed->getDomDocument()->getElementsByTagName('updated')->item(0)->textContent
        );
    }

    public function testFeedEncodingDefaultIsUsedIfEncodingNotSetByHand(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    public function testFeedTitleHasBeenSet(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('This is a test feed.', $feed->getTitle());
    }

    public function testFeedTitleIfMissingThrowsException(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validWriter->remove('title');

        $this->expectException(ExceptionInterface::class);
        $atomFeed->render();
    }

    /**
     * @group LaminasWCHARDATA01
     *
     */
    public function testFeedTitleCharDataEncoding(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validWriter->setTitle('<>&\'"áéíóú');
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getTitle());
    }

    public function testFeedSubtitleHasBeenSet(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('This is a test description.', $feed->getDescription());
    }

    /**
     * @doesNotPerformAssertions
     *
     */
    public function testFeedSubtitleThrowsNoExceptionIfMissing(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validWriter->remove('description');
        $atomFeed->render();
    }

    /**
     * @group LaminasWCHARDATA01
     *
     */
    public function testFeedSubtitleCharDataEncoding(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validWriter->setDescription('<>&\'"áéíóú');
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getDescription());
    }

    public function testFeedUpdatedDateHasBeenSet(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals(1234567890, $feed->getDateModified()->getTimestamp());
    }

    public function testFeedUpdatedDateIfMissingThrowsException(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validWriter->remove('dateModified');

        $this->expectException(ExceptionInterface::class);
        $atomFeed->render();
    }

    public function testFeedGeneratorHasBeenSet(): void
    {
        $this->validWriter->setGenerator('FooFeedBuilder', '1.00', 'http://www.example.com');
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('FooFeedBuilder', $feed->getGenerator());
    }

    /**
     * @doesNotPerformAssertions
     *
     */
    public function testFeedGeneratorIfMissingThrowsNoException(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validWriter->remove('generator');
        $atomFeed->render();
    }

    public function testFeedGeneratorDefaultIsUsedIfGeneratorNotSetByHand(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('Laminas_Feed_Writer', $feed->getGenerator());
    }

    /**
     * @group LaminasWCHARDATA01
     *
     */
    public function testFeedGeneratorCharDataEncoding(): void
    {
        $this->validWriter->setGenerator('<>&\'"áéíóú', '1.00', 'http://www.example.com');
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getGenerator());
    }

    public function testFeedLanguageHasBeenSet(): void
    {
        $this->validWriter->setLanguage('fr');
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('fr', $feed->getLanguage());
    }

    /**
     * @doesNotPerformAssertions
     *
     */
    public function testFeedLanguageIfMissingThrowsNoException(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validWriter->remove('language');
        $atomFeed->render();
    }

    public function testFeedLanguageDefaultIsUsedIfGeneratorNotSetByHand(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testFeedIncludesLinkToHtmlVersionOfFeed(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    /**
     * @doesNotPerformAssertions
     *
     */
    public function testFeedLinkToHtmlVersionOfFeedIfMissingThrowsNoExceptionIfIdSet(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validWriter->setId('http://www.example.com');
        $this->validWriter->remove('link');
        $atomFeed->render();
    }

    public function testFeedLinkToHtmlVersionOfFeedIfMissingThrowsExceptionIfIdMissing(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validWriter->remove('link');

        $this->expectException(ExceptionInterface::class);
        $atomFeed->render();
    }

    public function testFeedIncludesLinkToXmlAtomWhereTheFeedWillBeAvailable(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('http://www.example.com/atom', $feed->getFeedLink());
    }

    public function testFeedLinkToXmlAtomWhereTheFeedWillBeAvailableIfMissingThrowsException(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validWriter->remove('feedLinks');

        $this->expectException(ExceptionInterface::class);
        $atomFeed->render();
    }

    public function testFeedHoldsAnyAuthorAdded(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed   = Reader\Reader::importString($atomFeed->saveXml());
        $author = $feed->getAuthor();
        $this->assertEquals([
            'email' => 'joe@example.com',
            'name'  => 'Joe',
            'uri'   => 'http://www.example.com/joe',
        ], $feed->getAuthor());
    }

    /**
     * @group LaminasWCHARDATA01
     *
     */
    public function testFeedAuthorCharDataEncoding(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $this->validWriter->remove('authors');
        $this->validWriter->addAuthor([
            'email' => '<>&\'"áéíóú',
            'name'  => '<>&\'"áéíóú',
            'uri'   => 'http://www.example.com/joe',
        ]);
        $atomFeed->render();
        $feed   = Reader\Reader::importString($atomFeed->saveXml());
        $author = $feed->getAuthor();
        $this->assertEquals([
            'email' => '<>&\'"áéíóú',
            'name'  => '<>&\'"áéíóú',
            'uri'   => 'http://www.example.com/joe',
        ], $feed->getAuthor());
    }

    public function testFeedAuthorIfNotSetThrowsExceptionIfAnyEntriesAlsoAreMissingAuthors(): void
    {
        $this->markTestIncomplete('Not yet implemented...');
    }

    public function testFeedAuthorIfNotSetThrowsNoExceptionIfAllEntriesIncludeAtLeastOneAuthor(): void
    {
        $this->markTestIncomplete('Not yet implemented...');
    }

    public function testFeedIdHasBeenSet(): void
    {
        $this->validWriter->setId('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6', $feed->getId());
    }

    public function testFeedIdDefaultOfHtmlLinkIsUsedIfNotSetByHand(): void
    {
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals($feed->getLink(), $feed->getId());
    }

    public function testBaseUrlCanBeSet(): void
    {
        $this->validWriter->setBaseUrl('http://www.example.com/base');
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('http://www.example.com/base', $feed->getBaseUrl());
    }

    public function testCopyrightCanBeSet(): void
    {
        $this->validWriter->setCopyright('Copyright © 2009 Paddy');
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('Copyright © 2009 Paddy', $feed->getCopyright());
    }

    public function testCopyrightCharDataEncoding(): void
    {
        $this->validWriter->setCopyright('<>&\'"áéíóú');
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed = Reader\Reader::importString($atomFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getCopyright());
    }

    public function testCategoriesCanBeSet(): void
    {
        $this->validWriter->addCategories([
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
        $this->assertEquals($expected, (array) $feed->getCategories());
    }

    public function testCategoriesCharDataEncoding(): void
    {
        $this->validWriter->addCategories([
            [
                'term'   => 'cat_dog',
                'label'  => '<>&\'"áéíóú',
                'scheme' => 'http://example.com/schema1',
            ],
            ['term' => 'cat_dog2'],
        ]);
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed     = Reader\Reader::importString($atomFeed->saveXml());
        $expected = [
            [
                'term'   => 'cat_dog',
                'label'  => '<>&\'"áéíóú',
                'scheme' => 'http://example.com/schema1',
            ],
            [
                'term'   => 'cat_dog2',
                'label'  => 'cat_dog2',
                'scheme' => null,
            ],
        ];
        $this->assertEquals($expected, (array) $feed->getCategories());
    }

    public function testHubsCanBeSet(): void
    {
        $this->validWriter->addHubs(
            ['http://www.example.com/hub', 'http://www.example.com/hub2']
        );
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed     = Reader\Reader::importString($atomFeed->saveXml());
        $expected = [
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
        ];
        $this->assertEquals($expected, (array) $feed->getHubs());
    }

    public function testImageCanBeSet(): void
    {
        $this->validWriter->setImage(
            ['uri' => 'http://www.example.com/logo.gif']
        );
        $atomFeed = new Renderer\Feed\Atom($this->validWriter);
        $atomFeed->render();
        $feed     = Reader\Reader::importString($atomFeed->saveXml());
        $expected = [
            'uri' => 'http://www.example.com/logo.gif',
        ];
        $this->assertEquals($expected, $feed->getImage());
    }

    public function testFeedRendererEmitsNoticeDuringFeedImportWhenGooglePlayPodcastExtensionUnavailable(): void
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
        $renderer = new Renderer\Feed\Atom($this->validWriter);
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
