<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Reader;

use Interop\Container\ContainerInterface;
use Laminas\Feed\Reader;
use Laminas\Feed\Reader\Exception\InvalidArgumentException;
use Laminas\Feed\Reader\Feed\FeedInterface;
use Laminas\Feed\Reader\FeedSet;
use Laminas\Feed\Reader\Http\ClientInterface;
use Laminas\Feed\Reader\Http\ResponseInterface;
use Laminas\Http\Client\Adapter\Test as TestAdapter;
use Laminas\Http\Client as HttpClient;
use Laminas\Http\Response as HttpResponse;
use My\Extension\JungleBooks\Entry;
use My\Extension\JungleBooks\Feed;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Reader
 */
class ReaderTest extends TestCase
{
    protected $feedSamplePath;

    protected function setUp(): void
    {
        $this->feedSamplePath = dirname(__FILE__) . '/_files';
    }

    protected function tearDown(): void
    {
        Reader\Reader::reset();
    }

    public function testStringImportTrimsContentToAllowSlightlyInvalidXml(): void
    {
        $feed = Reader\Reader::importString(
            '   ' . file_get_contents($this->feedSamplePath . '/Reader/rss20.xml')
        );
        $this->assertInstanceOf(FeedInterface::class, $feed);
    }

    public function testDetectsFeedIsRss20(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/Reader/rss20.xml')
        );
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_20, $type);
    }

    public function testDetectsFeedIsRss094(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/Reader/rss094.xml')
        );
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_094, $type);
    }

    public function testDetectsFeedIsRss093(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/Reader/rss093.xml')
        );
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_093, $type);
    }

    public function testDetectsFeedIsRss092(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/Reader/rss092.xml')
        );
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_092, $type);
    }

    public function testDetectsFeedIsRss091(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/Reader/rss091.xml')
        );
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_091, $type);
    }

    public function testDetectsFeedIsRss10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/Reader/rss10.xml')
        );
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_10, $type);
    }

    public function testDetectsFeedIsRss090(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/Reader/rss090.xml')
        );
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_090, $type);
    }

    public function testDetectsFeedIsAtom10(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/Reader/atom10.xml')
        );
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_ATOM_10, $type);
    }

    public function testDetectsFeedIsAtom03(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents($this->feedSamplePath . '/Reader/atom03.xml')
        );
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_ATOM_03, $type);
    }

    /**
     * @group Laminas-9723
     * @codingStandardsIgnoreStart
     */
    public function testDetectsTypeFromStringOrToRemindPaddyAboutForgettingATestWhichLetsAStupidTypoSurviveUnnoticedForMonths(): void
    {
        $feed = '<?xml version="1.0" encoding="utf-8" ?><rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/"><channel></channel></rdf:RDF>';
        // @codingStandardsIgnoreEnd
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_10, $type);
    }

    public function testGetEncoding(): void
    {
        $feed = Reader\Reader::importString(
            file_get_contents(dirname(__FILE__) . '/Entry/_files/Atom/title/plain/atom10.xml')
        );

        $this->assertEquals('utf-8', $feed->getEncoding());
        $this->assertEquals('utf-8', $feed->current()->getEncoding());
    }

    public function testImportsFile(): void
    {
        $feed = Reader\Reader::importFile(
            dirname(__FILE__) . '/Entry/_files/Atom/title/plain/atom10.xml'
        );
        $this->assertInstanceOf(FeedInterface::class, $feed);
    }

    public function testImportsUri(): void
    {
        if (! getenv('TESTS_LAMINAS_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testImportsUri() requires a network connection');
        }

        $feed = Reader\Reader::import('https://github.com/laminas/laminas-feed/releases.atom');
        $this->assertInstanceOf(Reader\Feed\Atom::class, $feed);
    }

    /**
     * @group Laminas-8328
     *
     */
    public function testImportsUriAndThrowsExceptionIfNotAFeed(): void
    {
        if (! getenv('TESTS_LAMINAS_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testImportsUri() requires a network connection');
        }

        $this->expectException(Reader\Exception\RuntimeException::class);
        Reader\Reader::import('http://example.com');
    }

    public function testGetsFeedLinksAsValueObject(): void
    {
        if (! getenv('TESTS_LAMINAS_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testGetsFeedLinksAsValueObject() requires a network connection');
        }

        $links = Reader\Reader::findFeedLinks('https://github.com/laminas/laminas-feed/releases');
        $this->assertEquals('https://github.com/laminas/laminas-feed/releases.atom', $links->atom);
    }

    public function testCompilesLinksAsArrayObject(): void
    {
        if (! getenv('TESTS_LAMINAS_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testGetsFeedLinksAsValueObject() requires a network connection');
        }
        $links = Reader\Reader::findFeedLinks('https://github.com/laminas/laminas-feed/releases');
        $this->assertInstanceOf(FeedSet::class, $links);
        $this->assertEquals([
            'rel'   => 'alternate',
            'type'  => 'application/atom+xml',
            'href'  => 'https://github.com/laminas/laminas-feed/releases.atom',
            'title' => 'laminas-feed Release Notes',
        ], (array) $links->getIterator()->current());
    }

    public function testFeedSetLoadsFeedObjectWhenFeedArrayKeyAccessed(): void
    {
        if (! getenv('TESTS_LAMINAS_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testGetsFeedLinksAsValueObject() requires a network connection');
        }

        $links = Reader\Reader::findFeedLinks('https://github.com/laminas/laminas-feed/releases');
        $link  = $links->getIterator()->current();
        $this->assertInstanceOf(Reader\Feed\Atom::class, $link['feed']);
    }

    public function testZeroCountFeedSetReturnedFromEmptyList(): void
    {
        if (! getenv('TESTS_LAMINAS_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testGetsFeedLinksAsValueObject() requires a network connection');
        }

        $links = Reader\Reader::findFeedLinks('http://www.example.com');
        $this->assertCount(0, $links);
    }

    /**
     * @group Laminas-8327
     *
     */
    public function testGetsFeedLinksAndTrimsNewlines(): void
    {
        if (! getenv('TESTS_LAMINAS_FEED_READER_ONLINE_ENABLED')) {
            $this->markTestSkipped('testGetsFeedLinksAsValueObject() requires a network connection');
        }

        $links = Reader\Reader::findFeedLinks('https://github.com/laminas/laminas-feed/releases');
        $this->assertEquals('https://github.com/laminas/laminas-feed/releases.atom', $links->atom);
    }

    /**
     * @group Laminas-8330
     *
     */
    public function testGetsFeedLinksAndNormalisesRelativeUrlsOnUriWithPath(): void
    {
        $currClient = Reader\Reader::getHttpClient();

        $testAdapter = new TestAdapter();
        $response    = new HttpResponse();
        $response->setStatusCode(200);
        $response->setContent(
            '<!DOCTYPE html><html><head><link rel="alternate" type="application/rss+xml" '
            . 'href="../test.rss"><link rel="alternate" type="application/atom+xml" href="/test.atom"></head>'
            . '<body></body></html>'
        );
        $testAdapter->setResponse($response);
        Reader\Reader::setHttpClient(new HttpClient(null, ['adapter' => $testAdapter]));

        $links = Reader\Reader::findFeedLinks('http://foo/bar');

        Reader\Reader::setHttpClient($currClient);

        $this->assertEquals('http://foo/test.rss', $links->rss);
        $this->assertEquals('http://foo/test.atom', $links->atom);
    }

    public function testRegistersUserExtension(): void
    {
        require_once __DIR__ . '/_files/My/Extension/JungleBooks/Entry.php';
        require_once __DIR__ . '/_files/My/Extension/JungleBooks/Feed.php';
        $manager = new Reader\ExtensionManager(new Reader\ExtensionPluginManager(
            $this->getMockBuilder(ContainerInterface::class)->getMock()
        ));
        $manager->setInvokableClass('JungleBooks\Entry', Entry::class);
        $manager->setInvokableClass('JungleBooks\Feed', Feed::class);
        Reader\Reader::setExtensionManager($manager);
        Reader\Reader::registerExtension('JungleBooks');

        $this->assertTrue(Reader\Reader::isRegistered('JungleBooks'));
    }

    /**
     * This test is failing on windows:
     * Failed asserting that exception of type "Laminas\Feed\Reader\Exception\RuntimeException"
     * matches expected exception "Laminas\Feed\Reader\Exception\InvalidArgumentException".
     * Message was: "DOMDocument cannot parse XML: Entity 'discloseInfo' failed to parse".
     *
     * @todo why is the assertEquals commented out?
     *
     */
    public function testXxePreventionOnFeedParsing(): void
    {
        $string = file_get_contents($this->feedSamplePath . '/Reader/xxe-atom10.xml');
        $string = str_replace('XXE_URI', $this->feedSamplePath . '/Reader/xxe-info.txt', $string);

        $this->expectException(InvalidArgumentException::class);
        $feed = Reader\Reader::importString($string);
        //$this->assertEquals('info:', $feed->getTitle());
    }

    public function testImportRemoteFeedMethodPerformsAsExpected(): void
    {
        $uri          = 'http://example.com/feeds/reader.xml';
        $feedContents = file_get_contents($this->feedSamplePath . '/Reader/rss20.xml');
        $response     = $this->getMockBuilder(ResponseInterface::class)
            ->setMethods(['getStatusCode', 'getBody'])
            ->getMock();
        $response->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(200));
        $response->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($feedContents));

        $client = $this->getMockBuilder(ClientInterface::class)
            ->setMethods(['get'])
            ->getMock();
        $client->expects($this->once())
            ->method('get')
            ->with($this->equalTo($uri))
            ->will($this->returnValue($response));

        $feed = Reader\Reader::importRemoteFeed($uri, $client);
        $this->assertInstanceOf(FeedInterface::class, $feed);
        $type = Reader\Reader::detectType($feed);
        $this->assertEquals(Reader\Reader::TYPE_RSS_20, $type);
    }

    public function testImportStringMethodThrowProperExceptionOnEmptyString(): void
    {
        $string = ' ';

        $this->expectException(InvalidArgumentException::class);
        Reader\Reader::importString($string);
    }

    public function testSetHttpFeedClient(): void
    {
        $client = $this->createMock(ClientInterface::class);
        Reader\Reader::setHttpClient($client);
        $this->assertEquals($client, Reader\Reader::getHttpClient());
    }

    public function testSetHttpClientWillDecorateALaminasHttpClientInstance(): void
    {
        $client = new HttpClient();
        Reader\Reader::setHttpClient($client);
        $cached = Reader\Reader::getHttpClient();
        $this->assertInstanceOf(ClientInterface::class, $cached);
    }

    public function testSetHttpClientThrowsException(): void
    {
        $this->expectException(Reader\Exception\InvalidHttpClientException::class);
        Reader\Reader::setHttpClient(new stdClass());
    }

    public function testReaderEmitsNoticeDuringFeedImportWhenGooglePlayPodcastExtensionUnavailable(): void
    {
        Reader\Reader::setExtensionManager(new TestAsset\CustomExtensionManager());

        $notices = (object) [
            'messages' => [],
        ];

        set_error_handler(static function ($errno, $errstr) use ($notices) {
            $notices->messages[] = $errstr;
        }, \E_USER_NOTICE);
        $feed = Reader\Reader::importFile(
            dirname(__FILE__) . '/Entry/_files/Atom/title/plain/atom10.xml'
        );
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
