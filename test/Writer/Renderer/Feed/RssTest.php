<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Writer\Renderer\Feed;

use DateTime;
use DOMXPath;
use Laminas\Feed\Reader;
use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Exception\ExceptionInterface;
use Laminas\Feed\Writer\Feed;
use Laminas\Feed\Writer\Renderer;
use Laminas\Feed\Writer\Version;
use LaminasTest\Feed\Writer\TestAsset;
use PHPUnit\Framework\TestCase;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Writer
 */
class RssTest extends TestCase
{
    protected $validWriter;

    protected function setUp(): void
    {
        Writer\Writer::reset();
        $this->validWriter = new Writer\Feed();
        $this->validWriter->setTitle('This is a test feed.');
        $this->validWriter->setDescription('This is a test description.');
        $this->validWriter->setLink('http://www.example.com');

        $this->validWriter->setType('rss');
    }

    protected function tearDown(): void
    {
        Writer\Writer::reset();
        $this->validWriter = null;
    }

    public function testSetsWriterInConstructor()
    {
        $writer = new Writer\Feed();
        $feed   = new Renderer\Feed\Rss($writer);
        $this->assertInstanceOf(Feed::class, $feed->getDataContainer());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testBuildMethodRunsMinimalWriterContainerProperlyBeforeICheckRssCompliance()
    {
        $feed = new Renderer\Feed\Rss($this->validWriter);
        $feed->render();
    }

    public function testFeedEncodingHasBeenSet()
    {
        $this->validWriter->setEncoding('iso-8859-1');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('iso-8859-1', $feed->getEncoding());
    }

    public function testFeedEncodingDefaultIsUsedIfEncodingNotSetByHand()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('UTF-8', $feed->getEncoding());
    }

    public function testFeedTitleHasBeenSet()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('This is a test feed.', $feed->getTitle());
    }

    public function testFeedTitleIfMissingThrowsException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('title');

        $this->expectException(ExceptionInterface::class);
        $rssFeed->render();
    }

    /**
     * @group LaminasWCHARDATA01
     */
    public function testFeedTitleCharDataEncoding()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->setTitle('<>&\'"áéíóú');
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getTitle());
    }

    public function testFeedDescriptionHasBeenSet()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('This is a test description.', $feed->getDescription());
    }

    public function testFeedDescriptionThrowsExceptionIfMissing()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('description');

        $this->expectException(ExceptionInterface::class);
        $rssFeed->render();
    }

    /**
     * @group LaminasWCHARDATA01
     */
    public function testFeedDescriptionCharDataEncoding()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->setDescription('<>&\'"áéíóú');
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getDescription());
    }

    public function testFeedUpdatedDateHasBeenSet()
    {
        $this->validWriter->setDateModified(1234567890);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals(1234567890, $feed->getDateModified()->getTimestamp());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testFeedUpdatedDateIfMissingThrowsNoException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('dateModified');
        $rssFeed->render();
    }

    public function testFeedLastBuildDateHasBeenSet()
    {
        $this->validWriter->setLastBuildDate(1234567890);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals(1234567890, $feed->getLastBuildDate()->getTimestamp());
    }

    public function testFeedGeneratorHasBeenSet()
    {
        $this->validWriter->setGenerator('FooFeedBuilder', '1.00', 'http://www.example.com');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('FooFeedBuilder 1.00 (http://www.example.com)', $feed->getGenerator());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testFeedGeneratorIfMissingThrowsNoException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('generator');
        $rssFeed->render();
    }

    public function testFeedGeneratorDefaultIsUsedIfGeneratorNotSetByHand()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals(
            'Laminas_Feed_Writer ' . Version::VERSION . ' (https://getlaminas.org)',
            $feed->getGenerator()
        );
    }

    public function testFeedLanguageHasBeenSet()
    {
        $this->validWriter->setLanguage('fr');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('fr', $feed->getLanguage());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testFeedLanguageIfMissingThrowsNoException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('language');
        $rssFeed->render();
    }

    public function testFeedLanguageDefaultIsUsedIfGeneratorNotSetByHand()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals(null, $feed->getLanguage());
    }

    public function testFeedIncludesLinkToHtmlVersionOfFeed()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com', $feed->getLink());
    }

    public function testFeedLinkToHtmlVersionOfFeedIfMissingThrowsException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('link');

        $this->expectException(ExceptionInterface::class);
        $rssFeed->render();
    }

    /**
     * @group Issue2605
     */
    public function testFeedIncludesLinkToXmlRssWhereRssAndAtomLinksAreProvided()
    {
        $this->validWriter->setFeedLink('http://www.example.com/rss', 'rss');
        $this->validWriter->setFeedLink('http://www.example.com/atom', 'atom');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com/rss', $feed->getFeedLink());
        $xpath = new DOMXPath($feed->getDomDocument());
        $this->assertEquals(1, $xpath->evaluate('/rss/channel/atom:link[@rel="self"]')->length);
    }

    public function testFeedIncludesLinkToXmlRssWhereTheFeedWillBeAvailable()
    {
        $this->validWriter->setFeedLink('http://www.example.com/rss', 'rss');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com/rss', $feed->getFeedLink());
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testFeedLinkToXmlRssWhereTheFeedWillBeAvailableIfMissingThrowsNoException()
    {
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $this->validWriter->remove('feedLinks');
        $rssFeed->render();
    }

    public function testBaseUrlCanBeSet()
    {
        $this->validWriter->setBaseUrl('http://www.example.com/base');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('http://www.example.com/base', $feed->getBaseUrl());
    }

    /**
     * @group LaminasW003
     */
    public function testFeedHoldsAnyAuthorAdded()
    {
        $this->validWriter->addAuthor([
            'name'  => 'Joe',
            'email' => 'joe@example.com',
            'uri'   => 'http://www.example.com/joe',
        ]);
        $atomFeed = new Renderer\Feed\Rss($this->validWriter);
        $atomFeed->render();
        $feed   = Reader\Reader::importString($atomFeed->saveXml());
        $author = $feed->getAuthor();
        $this->assertEquals(['name' => 'Joe'], $feed->getAuthor());
    }

    /**
     * @group LaminasWCHARDATA01
     */
    public function testFeedAuthorCharDataEncoding()
    {
        $this->validWriter->addAuthor([
            'name'  => '<>&\'"áéíóú',
            'email' => 'joe@example.com',
            'uri'   => 'http://www.example.com/joe',
        ]);
        $atomFeed = new Renderer\Feed\Rss($this->validWriter);
        $atomFeed->render();
        $feed   = Reader\Reader::importString($atomFeed->saveXml());
        $author = $feed->getAuthor();
        $this->assertEquals(['name' => '<>&\'"áéíóú'], $feed->getAuthor());
    }

    public function testCopyrightCanBeSet()
    {
        $this->validWriter->setCopyright('Copyright © 2009 Paddy');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('Copyright © 2009 Paddy', $feed->getCopyright());
    }

    /**
     * @group LaminasWCHARDATA01
     */
    public function testCopyrightCharDataEncoding()
    {
        $this->validWriter->setCopyright('<>&\'"áéíóú');
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $this->assertEquals('<>&\'"áéíóú', $feed->getCopyright());
    }

    public function testCategoriesCanBeSet()
    {
        $this->validWriter->addCategories([
            [
                'term'   => 'cat_dog',
                'label'  => 'Cats & Dogs',
                'scheme' => 'http://example.com/schema1',
            ],
            ['term' => 'cat_dog2'],
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
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
        $this->assertEquals($expected, (array) $feed->getCategories());
    }

    /**
     * @group LaminasWCHARDATA01
     */
    public function testCategoriesCharDataEncoding()
    {
        $this->validWriter->addCategories([
            [
                'term'   => '<>&\'"áéíóú',
                'label'  => 'Cats & Dogs',
                'scheme' => 'http://example.com/schema1',
            ],
            ['term' => 'cat_dog2'],
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
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
        $this->assertEquals($expected, (array) $feed->getCategories());
    }

    public function testHubsCanBeSet()
    {
        $this->validWriter->addHubs(
            ['http://www.example.com/hub', 'http://www.example.com/hub2']
        );
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
        $expected = [
            'http://www.example.com/hub',
            'http://www.example.com/hub2',
        ];
        $this->assertEquals($expected, (array) $feed->getHubs());
    }

    public function testImageCanBeSet()
    {
        $this->validWriter->setImage([
            'uri'         => 'http://www.example.com/logo.gif',
            'link'        => 'http://www.example.com',
            'title'       => 'Image ALT',
            'height'      => '400',
            'width'       => '144',
            'description' => 'Image TITLE',
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
        $expected = [
            'uri'         => 'http://www.example.com/logo.gif',
            'link'        => 'http://www.example.com',
            'title'       => 'Image ALT',
            'height'      => '400',
            'width'       => '144',
            'description' => 'Image TITLE',
        ];
        $this->assertEquals($expected, $feed->getImage());
    }

    public function testImageCanBeSetWithOnlyRequiredElements()
    {
        $this->validWriter->setImage([
            'uri'   => 'http://www.example.com/logo.gif',
            'link'  => 'http://www.example.com',
            'title' => 'Image ALT',
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed     = Reader\Reader::importString($rssFeed->saveXml());
        $expected = [
            'uri'   => 'http://www.example.com/logo.gif',
            'link'  => 'http://www.example.com',
            'title' => 'Image ALT',
        ];
        $this->assertEquals($expected, $feed->getImage());
    }

    public function testImageThrowsExceptionOnMissingLink()
    {
        $this->validWriter->setImage([
            'uri'   => 'http://www.example.com/logo.gif',
            'title' => 'Image ALT',
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);

        $this->expectException(ExceptionInterface::class);
        $rssFeed->render();
    }

    public function testImageThrowsExceptionOnMissingTitle()
    {
        $this->validWriter->setImage([
            'uri'  => 'http://www.example.com/logo.gif',
            'link' => 'http://www.example.com',
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);

        $this->expectException(ExceptionInterface::class);
        $rssFeed->render();
    }

    public function testImageThrowsExceptionOnMissingUri()
    {
        $this->expectException(ExceptionInterface::class);
        $this->validWriter->setImage([
            'link'  => 'http://www.example.com',
            'title' => 'Image ALT',
        ]);
    }

    public function testImageThrowsExceptionIfOptionalDescriptionInvalid()
    {
        $this->validWriter->setImage([
            'uri'         => 'http://www.example.com/logo.gif',
            'link'        => 'http://www.example.com',
            'title'       => 'Image ALT',
            'description' => 2,
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);

        $this->expectException(ExceptionInterface::class);
        $rssFeed->render();
    }

    public function testImageThrowsExceptionIfOptionalDescriptionEmpty()
    {
        $this->validWriter->setImage([
            'uri'         => 'http://www.example.com/logo.gif',
            'link'        => 'http://www.example.com',
            'title'       => 'Image ALT',
            'description' => '',
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);

        $this->expectException(ExceptionInterface::class);
        $rssFeed->render();
    }

    public function testImageThrowsExceptionIfOptionalHeightNotAnInteger()
    {
        $this->validWriter->setImage([
            'uri'    => 'http://www.example.com/logo.gif',
            'link'   => 'http://www.example.com',
            'title'  => 'Image ALT',
            'height' => 'a',
            'width'  => 144,
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);

        $this->expectException(ExceptionInterface::class);
        $rssFeed->render();
    }

    public function testImageThrowsExceptionIfOptionalHeightEmpty()
    {
        $this->validWriter->setImage([
            'uri'    => 'http://www.example.com/logo.gif',
            'link'   => 'http://www.example.com',
            'title'  => 'Image ALT',
            'height' => '',
            'width'  => 144,
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);

        $this->expectException(ExceptionInterface::class);
        $rssFeed->render();
    }

    public function testImageThrowsExceptionIfOptionalHeightGreaterThan400()
    {
        $this->validWriter->setImage([
            'uri'    => 'http://www.example.com/logo.gif',
            'link'   => 'http://www.example.com',
            'title'  => 'Image ALT',
            'height' => '401',
            'width'  => 144,
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);

        $this->expectException(ExceptionInterface::class);
        $rssFeed->render();
    }

    public function testImageThrowsExceptionIfOptionalWidthNotAnInteger()
    {
        $this->validWriter->setImage([
            'uri'    => 'http://www.example.com/logo.gif',
            'link'   => 'http://www.example.com',
            'title'  => 'Image ALT',
            'height' => '400',
            'width'  => 'a',
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);

        $this->expectException(ExceptionInterface::class);
        $rssFeed->render();
    }

    public function testImageThrowsExceptionIfOptionalWidthEmpty()
    {
        $this->validWriter->setImage([
            'uri'    => 'http://www.example.com/logo.gif',
            'link'   => 'http://www.example.com',
            'title'  => 'Image ALT',
            'height' => '400',
            'width'  => '',
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);

        $this->expectException(ExceptionInterface::class);
        $rssFeed->render();
    }

    public function testImageThrowsExceptionIfOptionalWidthGreaterThan144()
    {
        $this->validWriter->setImage([
            'uri'    => 'http://www.example.com/logo.gif',
            'link'   => 'http://www.example.com',
            'title'  => 'Image ALT',
            'height' => '400',
            'width'  => '145',
        ]);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);

        $this->expectException(ExceptionInterface::class);
        $rssFeed->render();
    }

    public function testFeedSetDateCreated()
    {
        $this->validWriter->setDateCreated(1234567890);
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed   = Reader\Reader::importString($rssFeed->saveXml());
        $myDate = new DateTime('@' . 1234567890);
        $this->assertEquals($myDate, $feed->getDateCreated());
    }

    public function testFeedRendererEmitsNoticeDuringFeedImportWhenGooglePlayPodcastExtensionUnavailable()
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
        $renderer = new Renderer\Feed\Rss($this->validWriter);
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
