<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Writer;

use DateTime;
use DateTimeImmutable;
use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Deleted;
use Laminas\Feed\Writer\Entry;
use Laminas\Feed\Writer\Exception\ExceptionInterface;
use Laminas\Feed\Writer\Version;
use PHPUnit\Framework\TestCase;

/**
 * @group Laminas_Feed
 * @group Laminas_Feed_Writer
 */
class FeedTest extends TestCase
{
    protected $feedSamplePath;

    protected function setUp(): void
    {
        $this->feedSamplePath = dirname(__FILE__) . '/Writer/_files';
        Writer\Writer::reset();
    }

    protected function tearDown(): void
    {
        Writer\Writer::reset();
    }

    public function testAddsAuthorNameFromArray(): void
    {
        $writer = new Writer\Feed();
        $writer->addAuthor(['name' => 'Joe']);
        $this->assertEquals(['name' => 'Joe'], $writer->getAuthor());
    }

    public function testAddsAuthorEmailFromArray(): void
    {
        $writer = new Writer\Feed();
        $writer->addAuthor([
            'name'  => 'Joe',
            'email' => 'joe@example.com',
        ]);
        $this->assertEquals([
            'name'  => 'Joe',
            'email' => 'joe@example.com',
        ], $writer->getAuthor());
    }

    public function testAddsAuthorUriFromArray(): void
    {
        $writer = new Writer\Feed();
        $writer->addAuthor([
            'name' => 'Joe',
            'uri'  => 'http://www.example.com',
        ]);
        $this->assertEquals([
            'name' => 'Joe',
            'uri'  => 'http://www.example.com',
        ], $writer->getAuthor());
    }

    public function testAddAuthorThrowsExceptionOnInvalidNameFromArray(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $writer->addAuthor(['name' => '']);
    }

    public function testAddAuthorThrowsExceptionOnInvalidEmailFromArray(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $writer->addAuthor([
            'name'  => 'Joe',
            'email' => '',
        ]);
    }

    public function testAddAuthorThrowsExceptionOnInvalidUriFromArray(): void
    {
        $this->markTestIncomplete('Pending Laminas\URI fix for validation');

        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $writer->addAuthor([
            'name' => 'Joe',
            'uri'  => 'notauri',
        ]);
    }

    public function testAddAuthorThrowsExceptionIfNameOmittedFromArray(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $writer->addAuthor(['uri' => 'notauri']);
    }

    public function testAddsAuthorsFromArrayOfAuthors(): void
    {
        $writer = new Writer\Feed();
        $writer->addAuthors([
            [
                'name' => 'Joe',
                'uri'  => 'http://www.example.com',
            ],
            [
                'name' => 'Jane',
                'uri'  => 'http://www.example.com',
            ],
        ]);
        $this->assertEquals([
            'name' => 'Jane',
            'uri'  => 'http://www.example.com',
        ], $writer->getAuthor(1));
    }

    public function testSetsCopyright(): void
    {
        $writer = new Writer\Feed();
        $writer->setCopyright('Copyright (c) 2009 Paddy Brady');
        $this->assertEquals('Copyright (c) 2009 Paddy Brady', $writer->getCopyright());
    }

    public function testSetCopyrightThrowsExceptionOnInvalidParam(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setCopyright('');
    }

    public function testSetDateCreatedDefaultsToCurrentTime(): void
    {
        $writer = new Writer\Feed();
        $writer->setDateCreated();
        $dateNow = new DateTime();
        $this->assertLessThanOrEqual($dateNow, $writer->getDateCreated());
    }

    public function testSetDateCreatedUsesGivenUnixTimestamp(): void
    {
        $writer = new Writer\Feed();
        $writer->setDateCreated(1234567890);
        $myDate = new DateTime('@' . 1234567890);
        $this->assertEquals($myDate, $writer->getDateCreated());
    }

    /**
     * @group Laminas-12023
     *
     */
    public function testSetDateCreatedUsesGivenUnixTimestampThatIsLessThanTenDigits(): void
    {
        $writer = new Writer\Feed();
        $writer->setDateCreated(123456789);
        $myDate = new DateTime('@' . 123456789);
        $this->assertEquals($myDate, $writer->getDateCreated());
    }

    /**
     * @group Laminas-11610
     *
     */
    public function testSetDateCreatedUsesGivenUnixTimestampThatIsAVerySmallInteger(): void
    {
        $writer = new Writer\Feed();
        $writer->setDateCreated(123);
        $myDate = new DateTime('@' . 123);
        $this->assertEquals($myDate, $writer->getDateCreated());
    }

    public function testSetDateCreatedUsesDateTimeObject(): void
    {
        $myDate = new DateTime('@' . 1234567890);
        $writer = new Writer\Feed();
        $writer->setDateCreated($myDate);
        $this->assertEquals($myDate, $writer->getDateCreated());
    }

    public function testSetDateCreatedUsesDateTimeImmutableObject(): void
    {
        $myDate = new DateTimeImmutable('@' . 1234567890);
        $writer = new Writer\Feed();
        $writer->setDateCreated($myDate);
        $this->assertEquals($myDate, $writer->getDateCreated());
    }

    public function testSetDateModifiedDefaultsToCurrentTime(): void
    {
        $writer = new Writer\Feed();
        $writer->setDateModified();
        $dateNow = new DateTime();
        $this->assertLessThanOrEqual($dateNow, $writer->getDateModified());
    }

    public function testSetDateModifiedUsesGivenUnixTimestamp(): void
    {
        $writer = new Writer\Feed();
        $writer->setDateModified(1234567890);
        $myDate = new DateTime('@' . 1234567890);
        $this->assertEquals($myDate, $writer->getDateModified());
    }

    /**
     * @group Laminas-12023
     *
     */
    public function testSetDateModifiedUsesGivenUnixTimestampThatIsLessThanTenDigits(): void
    {
        $writer = new Writer\Feed();
        $writer->setDateModified(123456789);
        $myDate = new DateTime('@' . 123456789);
        $this->assertEquals($myDate, $writer->getDateModified());
    }

    /**
     * @group Laminas-11610
     *
     */
    public function testSetDateModifiedUsesGivenUnixTimestampThatIsAVerySmallInteger(): void
    {
        $writer = new Writer\Feed();
        $writer->setDateModified(123);
        $myDate = new DateTime('@' . 123);
        $this->assertEquals($myDate, $writer->getDateModified());
    }

    public function testSetDateModifiedUsesDateTimeObject(): void
    {
        $myDate = new DateTime('@' . 1234567890);
        $writer = new Writer\Feed();
        $writer->setDateModified($myDate);
        $this->assertEquals($myDate, $writer->getDateModified());
    }

    public function testSetDateModifiedUsesDateTimeImmutableObject(): void
    {
        $myDate = new DateTimeImmutable('@' . 1234567890);
        $writer = new Writer\Feed();
        $writer->setDateModified($myDate);
        $this->assertEquals($myDate, $writer->getDateModified());
    }

    public function testSetDateCreatedThrowsExceptionOnInvalidParameter(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setDateCreated('abc');
    }

    public function testSetDateModifiedThrowsExceptionOnInvalidParameter(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setDateModified('abc');
    }

    public function testGetDateCreatedReturnsNullIfDateNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getDateCreated());
    }

    public function testGetDateModifiedReturnsNullIfDateNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getDateModified());
    }

    public function testSetLastBuildDateDefaultsToCurrentTime(): void
    {
        $writer = new Writer\Feed();
        $writer->setLastBuildDate();
        $dateNow = new DateTime();
        $this->assertLessThanOrEqual($dateNow, $writer->getLastBuildDate());
    }

    public function testSetLastBuildDateUsesGivenUnixTimestamp(): void
    {
        $writer = new Writer\Feed();
        $writer->setLastBuildDate(1234567890);
        $myDate = new DateTime('@' . 1234567890);
        $this->assertEquals($myDate, $writer->getLastBuildDate());
    }

    /**
     * @group Laminas-12023
     *
     */
    public function testSetLastBuildDateUsesGivenUnixTimestampThatIsLessThanTenDigits(): void
    {
        $writer = new Writer\Feed();
        $writer->setLastBuildDate(123456789);
        $myDate = new DateTime('@' . 123456789);
        $this->assertEquals($myDate, $writer->getLastBuildDate());
    }

    /**
     * @group Laminas-11610
     *
     */
    public function testSetLastBuildDateUsesGivenUnixTimestampThatIsAVerySmallInteger(): void
    {
        $writer = new Writer\Feed();
        $writer->setLastBuildDate(123);
        $myDate = new DateTime('@' . 123);
        $this->assertEquals($myDate, $writer->getLastBuildDate());
    }

    public function testSetLastBuildDateUsesDateTimeObject(): void
    {
        $myDate = new DateTime('@' . 1234567890);
        $writer = new Writer\Feed();
        $writer->setLastBuildDate($myDate);
        $this->assertEquals($myDate, $writer->getLastBuildDate());
    }

    public function testSetLastBuildDateUsesDateTimeImmutableObject(): void
    {
        $myDate = new DateTimeImmutable('@' . 1234567890);
        $writer = new Writer\Feed();
        $writer->setLastBuildDate($myDate);
        $this->assertEquals($myDate, $writer->getLastBuildDate());
    }

    public function testSetLastBuildDateThrowsExceptionOnInvalidParameter(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setLastBuildDate('abc');
    }

    public function testGetLastBuildDateReturnsNullIfDateNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getLastBuildDate());
    }

    public function testGetCopyrightReturnsNullIfDateNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getCopyright());
    }

    public function testSetsDescription(): void
    {
        $writer = new Writer\Feed();
        $writer->setDescription('abc');
        $this->assertEquals('abc', $writer->getDescription());
    }

    public function testSetDescriptionThrowsExceptionOnInvalidParameter(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setDescription('');
    }

    public function testGetDescriptionReturnsNullIfDateNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getDescription());
    }

    public function testSetsId(): void
    {
        $writer = new Writer\Feed();
        $writer->setId('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $writer->getId());
    }

    public function testSetsIdAcceptsUrns(): void
    {
        $writer = new Writer\Feed();
        $writer->setId('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6');
        $this->assertEquals('urn:uuid:60a76c80-d399-11d9-b93C-0003939e0af6', $writer->getId());
    }

    public function testSetsIdAcceptsSimpleTagUri(): void
    {
        $writer = new Writer\Feed();
        $writer->setId('tag:example.org,2010:/foo/bar/');
        $this->assertEquals('tag:example.org,2010:/foo/bar/', $writer->getId());
    }

    public function testSetsIdAcceptsComplexTagUri(): void
    {
        $writer = new Writer\Feed();
        $writer->setId('tag:diveintomark.org,2004-05-27:/archives/2004/05/27/howto-atom-linkblog');
        $this->assertEquals(
            'tag:diveintomark.org,2004-05-27:/archives/2004/05/27/howto-atom-linkblog',
            $writer->getId()
        );
    }

    public function testSetIdThrowsExceptionOnInvalidParameter(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setId('');
    }

    public function testSetIdThrowsExceptionOnInvalidUri(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setId('http://');
    }

    public function testGetIdReturnsNullIfDateNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getId());
    }

    public function testSetsLanguage(): void
    {
        $writer = new Writer\Feed();
        $writer->setLanguage('abc');
        $this->assertEquals('abc', $writer->getLanguage());
    }

    public function testSetLanguageThrowsExceptionOnInvalidParameter(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setLanguage('');
    }

    public function testGetLanguageReturnsNullIfDateNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getLanguage());
    }

    public function testSetsLink(): void
    {
        $writer = new Writer\Feed();
        $writer->setLink('http://www.example.com/id');
        $this->assertEquals('http://www.example.com/id', $writer->getLink());
    }

    public function testSetLinkThrowsExceptionOnEmptyString(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setLink('');
    }

    public function testSetLinkThrowsExceptionOnInvalidUri(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setLink('http://');
    }

    public function testGetLinkReturnsNullIfDateNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getLink());
    }

    public function testSetsEncoding(): void
    {
        $writer = new Writer\Feed();
        $writer->setEncoding('utf-16');
        $this->assertEquals('utf-16', $writer->getEncoding());
    }

    public function testSetEncodingThrowsExceptionOnInvalidParameter(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setEncoding('');
    }

    public function testGetEncodingReturnsUtf8IfNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertEquals('UTF-8', $writer->getEncoding());
    }

    public function testSetsTitle(): void
    {
        $writer = new Writer\Feed();
        $writer->setTitle('abc');
        $this->assertEquals('abc', $writer->getTitle());
    }

    public function testSetTitleThrowsExceptionOnInvalidParameter(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setTitle('');
    }

    public function testGetTitleReturnsNullIfDateNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getTitle());
    }

    public function testSetsGeneratorName(): void
    {
        $writer = new Writer\Feed();
        $writer->setGenerator(['name' => 'LaminasW']);
        $this->assertEquals(['name' => 'LaminasW'], $writer->getGenerator());
    }

    public function testSetsGeneratorVersion(): void
    {
        $writer = new Writer\Feed();
        $writer->setGenerator([
            'name'    => 'LaminasW',
            'version' => '1.0',
        ]);
        $this->assertEquals([
            'name'    => 'LaminasW',
            'version' => '1.0',
        ], $writer->getGenerator());
    }

    public function testSetsGeneratorUri(): void
    {
        $writer = new Writer\Feed();
        $writer->setGenerator([
            'name' => 'LaminasW',
            'uri'  => 'http://www.example.com',
        ]);
        $this->assertEquals([
            'name' => 'LaminasW',
            'uri'  => 'http://www.example.com',
        ], $writer->getGenerator());
    }

    public function testSetsGeneratorThrowsExceptionOnInvalidName(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setGenerator([]);
    }

    public function testSetsGeneratorThrowsExceptionOnInvalidVersion(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setGenerator([
            'name'    => 'LaminasW',
            'version' => '',
        ]);
    }

    public function testSetsGeneratorThrowsExceptionOnInvalidUri(): void
    {
        $this->markTestIncomplete('Pending Laminas\URI fix for validation');

        $writer = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $writer->setGenerator([
            'name' => 'LaminasW',
            'uri'  => 'notauri',
        ]);
    }

    /**
     * @deprecated
     *
     */
    public function testSetsGeneratorNameDeprecated(): void
    {
        $writer = new Writer\Feed();
        $writer->setGenerator('LaminasW');
        $this->assertEquals(['name' => 'LaminasW'], $writer->getGenerator());
    }

    /**
     * @deprecated
     *
     */
    public function testSetsGeneratorVersionDeprecated(): void
    {
        $writer = new Writer\Feed();
        $writer->setGenerator('LaminasW', '1.0');
        $this->assertEquals([
            'name'    => 'LaminasW',
            'version' => '1.0',
        ], $writer->getGenerator());
    }

    /**
     * @deprecated
     *
     */
    public function testSetsGeneratorUriDeprecated(): void
    {
        $writer = new Writer\Feed();
        $writer->setGenerator('LaminasW', null, 'http://www.example.com');
        $this->assertEquals([
            'name' => 'LaminasW',
            'uri'  => 'http://www.example.com',
        ], $writer->getGenerator());
    }

    /**
     * @deprecated
     *
     */
    public function testSetsGeneratorThrowsExceptionOnInvalidNameDeprecated(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setGenerator('');
    }

    /**
     * @deprecated
     *
     */
    public function testSetsGeneratorThrowsExceptionOnInvalidVersionDeprecated(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setGenerator('LaminasW', '');
    }

    /**
     * @deprecated
     *
     */
    public function testSetsGeneratorThrowsExceptionOnInvalidUriDeprecated(): void
    {
        $this->markTestIncomplete('Pending Laminas\URI fix for validation');

        $writer = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $writer->setGenerator('LaminasW', null, 'notauri');
    }

    public function testGetGeneratorReturnsNullIfDateNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getGenerator());
    }

    public function testSetsFeedLink(): void
    {
        $writer = new Writer\Feed();
        $writer->setFeedLink('http://www.example.com/rss', 'RSS');
        $this->assertEquals(['rss' => 'http://www.example.com/rss'], $writer->getFeedLinks());
    }

    public function testSetsFeedLinkThrowsExceptionOnInvalidType(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setFeedLink('http://www.example.com/rss', 'abc');
    }

    public function testSetsFeedLinkThrowsExceptionOnInvalidUri(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setFeedLink('http://', 'rss');
    }

    public function testGetFeedLinksReturnsNullIfNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getFeedLinks());
    }

    public function testSetsBaseUrl(): void
    {
        $writer = new Writer\Feed();
        $writer->setBaseUrl('http://www.example.com');
        $this->assertEquals('http://www.example.com', $writer->getBaseUrl());
    }

    public function testSetsBaseUrlThrowsExceptionOnInvalidUri(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->setBaseUrl('http://');
    }

    public function testGetBaseUrlReturnsNullIfNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getBaseUrl());
    }

    public function testAddsHubUrl(): void
    {
        $writer = new Writer\Feed();
        $writer->addHub('http://www.example.com/hub');
        $this->assertEquals(['http://www.example.com/hub'], $writer->getHubs());
    }

    public function testAddsManyHubUrls(): void
    {
        $writer = new Writer\Feed();
        $writer->addHubs(['http://www.example.com/hub', 'http://www.example.com/hub2']);
        $this->assertEquals(['http://www.example.com/hub', 'http://www.example.com/hub2'], $writer->getHubs());
    }

    public function testAddingHubUrlThrowsExceptionOnInvalidUri(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->addHub('http://');
    }

    public function testAddingHubUrlReturnsNullIfNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getHubs());
    }

    public function testCreatesNewEntryDataContainer(): void
    {
        $writer = new Writer\Feed();
        $entry  = $writer->createEntry();
        $this->assertInstanceOf(Entry::class, $entry);
    }

    public function testAddsCategory(): void
    {
        $writer = new Writer\Feed();
        $writer->addCategory(['term' => 'cat_dog']);
        $this->assertEquals([['term' => 'cat_dog']], $writer->getCategories());
    }

    public function testAddsManyCategories(): void
    {
        $writer = new Writer\Feed();
        $writer->addCategories([['term' => 'cat_dog'], ['term' => 'cat_mouse']]);
        $this->assertEquals([['term' => 'cat_dog'], ['term' => 'cat_mouse']], $writer->getCategories());
    }

    public function testAddingCategoryWithoutTermThrowsException(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->addCategory([
            'label'  => 'Cats & Dogs',
            'scheme' => 'http://www.example.com/schema1',
        ]);
    }

    public function testAddingCategoryWithInvalidUriAsSchemeThrowsException(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\ExceptionInterface::class);
        $writer->addCategory([
            'term'   => 'cat_dog',
            'scheme' => 'http://',
        ]);
    }

    // Image Tests

    public function testSetsImageUri(): void
    {
        $writer = new Writer\Feed();
        $writer->setImage([
            'uri' => 'http://www.example.com/logo.gif',
        ]);
        $this->assertEquals([
            'uri' => 'http://www.example.com/logo.gif',
        ], $writer->getImage());
    }

    public function testSetsImageUriThrowsExceptionOnEmptyUri(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $writer->setImage([
            'uri' => '',
        ]);
    }

    public function testSetsImageUriThrowsExceptionOnMissingUri(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $writer->setImage([]);
    }

    public function testSetsImageUriThrowsExceptionOnInvalidUri(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(ExceptionInterface::class);
        $writer->setImage([
            'uri' => 'http://',
        ]);
    }

    public function testSetsImageLink(): void
    {
        $writer = new Writer\Feed();
        $writer->setImage([
            'uri'  => 'http://www.example.com/logo.gif',
            'link' => 'http://www.example.com',
        ]);
        $this->assertEquals([
            'uri'  => 'http://www.example.com/logo.gif',
            'link' => 'http://www.example.com',
        ], $writer->getImage());
    }

    public function testSetsImageTitle(): void
    {
        $writer = new Writer\Feed();
        $writer->setImage([
            'uri'   => 'http://www.example.com/logo.gif',
            'title' => 'Image title',
        ]);
        $this->assertEquals([
            'uri'   => 'http://www.example.com/logo.gif',
            'title' => 'Image title',
        ], $writer->getImage());
    }

    public function testSetsImageHeight(): void
    {
        $writer = new Writer\Feed();
        $writer->setImage([
            'uri'    => 'http://www.example.com/logo.gif',
            'height' => '88',
        ]);
        $this->assertEquals([
            'uri'    => 'http://www.example.com/logo.gif',
            'height' => '88',
        ], $writer->getImage());
    }

    public function testSetsImageWidth(): void
    {
        $writer = new Writer\Feed();
        $writer->setImage([
            'uri'   => 'http://www.example.com/logo.gif',
            'width' => '88',
        ]);
        $this->assertEquals([
            'uri'   => 'http://www.example.com/logo.gif',
            'width' => '88',
        ], $writer->getImage());
    }

    public function testSetsImageDescription(): void
    {
        $writer = new Writer\Feed();
        $writer->setImage([
            'uri'         => 'http://www.example.com/logo.gif',
            'description' => 'Image description',
        ]);
        $this->assertEquals([
            'uri'         => 'http://www.example.com/logo.gif',
            'description' => 'Image description',
        ], $writer->getImage());
    }

    public function testGetCategoriesReturnsNullIfNotSet(): void
    {
        $writer = new Writer\Feed();
        $this->assertNull($writer->getCategories());
    }

    public function testAddsAndOrdersEntriesByDateIfRequested(): void
    {
        $writer = new Writer\Feed();
        $entry  = $writer->createEntry();
        $entry->setDateCreated(1234567890);
        $entry2 = $writer->createEntry();
        $entry2->setDateCreated(1230000000);
        $writer->addEntry($entry);
        $writer->addEntry($entry2);
        $writer->orderByDate();
        $this->assertEquals(1230000000, $writer->getEntry(1)->getDateCreated()->getTimestamp());
    }

    /**
     * @covers \Laminas\Feed\Writer\Feed::orderByDate
     *
     */
    public function testAddsAndOrdersEntriesByModifiedDate(): void
    {
        $writer = new Writer\Feed();
        $entry  = $writer->createEntry();
        $entry->setDateModified(1234567890);
        $entry2 = $writer->createEntry();
        $entry2->setDateModified(1230000000);
        $writer->addEntry($entry);
        $writer->addEntry($entry2);
        $writer->orderByDate();
        $this->assertEquals(1230000000, $writer->getEntry(1)->getDateModified()->getTimestamp());
    }

    /**
     * @covers \Laminas\Feed\Writer\Feed::getEntry
     *
     */
    public function testGetEntry(): void
    {
        $writer = new Writer\Feed();
        $entry  = $writer->createEntry();
        $entry->setTitle('foo');
        $writer->addEntry($entry);
        $this->assertEquals('foo', $writer->getEntry()->getTitle());
    }

    /**
     * @covers \Laminas\Feed\Writer\Feed::removeEntry
     *
     */
    public function testGetEntryException(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $writer->getEntry(1);
    }

    /**
     * @covers \Laminas\Feed\Writer\Feed::removeEntry
     *
     */
    public function testRemoveEntry(): void
    {
        $writer = new Writer\Feed();
        $entry  = $writer->createEntry();
        $entry->setDateCreated(1234567890);
        $entry2 = $writer->createEntry();
        $entry2->setDateCreated(1230000000);
        $entry3 = $writer->createEntry();
        $entry3->setDateCreated(1239999999);

        $writer->addEntry($entry);
        $writer->addEntry($entry2);
        $writer->addEntry($entry3);
        $writer->orderByDate();
        $this->assertEquals('1234567890', $writer->getEntry(1)->getDateCreated()->getTimestamp());

        $writer->removeEntry(1);
        $writer->orderByDate();
        $this->assertEquals('1230000000', $writer->getEntry(1)->getDateCreated()->getTimestamp());
    }

    /**
     * @covers \Laminas\Feed\Writer\Feed::removeEntry
     *
     */
    public function testRemoveEntryException(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $writer->removeEntry(1);
    }

    /**
     * @covers \Laminas\Feed\Writer\Feed::createTombstone
     *
     * @return Deleted
     */
    public function testCreateTombstone(): Deleted
    {
        $writer    = new Writer\Feed();
        $tombstone = $writer->createTombstone();

        $this->assertInstanceOf(Deleted::class, $tombstone);

        return $tombstone;
    }

    /**
     * @covers \Laminas\Feed\Writer\Feed::addTombstone
     *
     */
    public function testAddTombstone(): void
    {
        $writer    = new Writer\Feed();
        $tombstone = $writer->createTombstone();
        $writer->addTombstone($tombstone);

        $this->assertInstanceOf(Deleted::class, $writer->getEntry(0));
    }

    /**
     * @covers \Laminas\Feed\Writer\Feed::export
     *
     */
    public function testExportRss(): void
    {
        $writer = new Writer\Feed();
        $writer->setTitle('foo');
        $writer->setDescription('bar');
        $writer->setLink('http://www.example.org');

        $export = $writer->export('rss');

        $feed = <<<'EOT'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title>foo</title>
    <description>bar</description>
    <generator>Laminas_Feed_Writer %version% (https://getlaminas.org)</generator>
    <link>http://www.example.org</link>
  </channel>
</rss>

EOT;
        $feed = str_replace('%version%', Version::VERSION, $feed);
        $feed = str_replace("\r\n", "\n", $feed);
        $this->assertEquals($feed, $export);
    }

    /**
     * @covers \Laminas\Feed\Writer\Feed::export
     *
     */
    public function testExportRssIgnoreExceptions(): void
    {
        $writer = new Writer\Feed();
        $export = $writer->export('rss', true);

        $feed = <<<'EOT'
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <generator>Laminas_Feed_Writer %version% (https://getlaminas.org)</generator>
  </channel>
</rss>

EOT;
        $feed = str_replace('%version%', Version::VERSION, $feed);
        $feed = str_replace("\r\n", "\n", $feed);
        $this->assertEquals($feed, $export);
    }

    /**
     * @covers \Laminas\Feed\Writer\Feed::export
     *
     */
    public function testExportWrongTypeException(): void
    {
        $writer = new Writer\Feed();

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $writer->export('foo');
    }

    public function testFluentInterface(): void
    {
        $writer = new Writer\Feed();
        $return = $writer->addAuthor(['name' => 'foo'])
            ->addAuthors([['name' => 'foo']])
            ->setCopyright('copyright')
            ->addCategories([['term' => 'foo']])
            ->addCategory(['term' => 'foo'])
            ->addHub('foo')
            ->addHubs(['foo'])
            ->setBaseUrl('http://www.example.com')
            ->setDateCreated(null)
            ->setDateModified(null)
            ->setDescription('description')
            ->setEncoding('utf-8')
            ->setId('1')
            ->setImage(['uri' => 'http://www.example.com'])
            ->setLanguage('fr')
            ->setLastBuildDate(null)
            ->setLink('foo')
            ->setTitle('foo')
            ->setType('foo');

        $this->assertSame($return, $writer);
    }

    public function testSetTitleShouldAllowAStringWithTheContentsZero(): void
    {
        $feed = new Writer\Feed();
        $feed->setTitle('0');
        $this->assertEquals('0', $feed->getTitle());
    }

    public function testFeedWriterEmitsNoticeDuringFeedImportWhenGooglePlayPodcastExtensionUnavailable(): void
    {
        Writer\Writer::setExtensionManager(new TestAsset\CustomExtensionManager());

        $notices = (object) [
            'messages' => [],
        ];

        set_error_handler(static function ($errno, $errstr) use ($notices) {
            $notices->messages[] = $errstr;
        }, \E_USER_NOTICE);
        $writer = new Writer\Feed();
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
