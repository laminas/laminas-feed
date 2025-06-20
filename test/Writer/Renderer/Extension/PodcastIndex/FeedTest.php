<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Writer\Renderer\Extension\PodcastIndex;

use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Renderer;
use PHPUnit\Framework\TestCase;

use function implode;

class FeedTest extends TestCase
{
    protected Writer\Feed $validWriter;

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
    }

    public function testRendersRssLockedTag(): void
    {
        $locked = [
            'value' => 'yes',
            'owner' => 'john.doe@example.com',
        ];
        $this->validWriter->setPodcastIndexLocked($locked);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();
        $this->assertStringContainsString('<podcast:locked', $xml);
    }

    public function testRendersRssFundingTag(): void
    {
        $funding = [
            'title' => 'Support the show!',
            'url'   => 'http://example.com/donate',
        ];
        $this->validWriter->setPodcastIndexFunding($funding);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:funding', $xml);
    }

    public function testRendersRssLicenseTag(): void
    {
        $identifier = 'cc-by-4.0';
        $url        = 'https://spdx.org/licenses/CC-BY-4.0.html';

        $license = [
            'identifier' => $identifier,
            'url'        => $url,
        ];
        $this->validWriter->setPodcastIndexLicense($license);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:license', $xml);
        $this->assertStringContainsString($url, $xml);
        $this->assertStringContainsString($identifier, $xml);
    }

    public function testRendersRssLocationTag(): void
    {
        $location = [
            'description' => 'London, Baker Street',
            'geo'         => 'geo:-27.86159,153.3169',
            'osm'         => 'W43678282',
        ];
        $this->validWriter->setPodcastIndexLocation($location);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:location', $xml);
        $this->assertStringContainsString($location['description'], $xml);
        $this->assertStringContainsString($location['geo'], $xml);
        $this->assertStringContainsString($location['osm'], $xml);
    }

    public function testRendersRssImagesTag(): void
    {
        $srcset = [
            "https://example.com/images/ep1/pci_avatar-massive.jpg 1500w",
            "https://example.com/images/ep1/pci_avatar-middle.jpg 600w",
            "https://example.com/images/ep1/pci_avatar-small.jpg 300w",
            "https://example.com/images/ep1/pci_avatar-tiny.jpg 150w",
        ];
        $images = [
            'srcset' => implode(", ", $srcset), // cast to string
        ];

        $this->validWriter->setPodcastIndexImages($images);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:images', $xml);
        $this->assertStringContainsString($images['srcset'], $xml);
    }

    public function testRendersRssUpdateFrequencyTag(): void
    {
        $description = 'Daily';
        $complete    = false;

        $updateFrequency = [
            'description' => $description,
            'complete'    => $complete,
            'dtstart'     => '2023-08-28T00:00:00.000Z',
            'rrule'       => 'FREQ=DAILY',
        ];

        $this->validWriter->setPodcastIndexUpdateFrequency($updateFrequency);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:updateFrequency', $xml);
        $this->assertStringContainsString(">$description<", $xml);
        $this->assertStringContainsString((string) $complete, $xml);
        $this->assertStringContainsString($updateFrequency['dtstart'], $xml);
        $this->assertStringContainsString($updateFrequency['rrule'], $xml);
    }

    public function testRendersRssPersonTag(): void
    {
        $person = [
            'name'  => 'Hercules Poirot',
            'role'  => 'guest',
            'group' => 'writing',
            'img'   => 'https://poirot.com/about/my-moustage.jpg',
            'href'  => 'https://poirot.com/my-cases',
        ];

        $this->validWriter->addPodcastIndexPerson($person);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:person', $xml);
        $this->assertStringContainsString($person['name'], $xml);
        $this->assertStringContainsString($person['role'], $xml);
        $this->assertStringContainsString($person['group'], $xml);
        $this->assertStringContainsString($person['img'], $xml);
        $this->assertStringContainsString($person['href'], $xml);
    }

    public function testRendersMultipleRssPersonTags(): void
    {
        $fName = 'Hercules Poirot';
        $sName = 'Agatha Christie';

        $people = [
            ['name' => $fName],
            ['name' => $sName],
        ];

        $this->validWriter->setPodcastIndexPeople($people);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString(">$fName</podcast:person>", $xml);
        $this->assertStringContainsString(">$sName</podcast:person>", $xml);
    }

    public function testRendersRssTrailerTag(): void
    {
        $trailer = [
            'title'   => 'Season 4: Race for the Clouds',
            'pubdate' => "Thu, 01 Apr 2021 08:00:00 EST",
            'url'     => "https://example.org/season4teaser.mp4",
        ];
        $this->validWriter->setPodcastIndexTrailer($trailer);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:trailer', $xml);
        $this->assertStringContainsString($trailer['title'], $xml);
        $this->assertStringContainsString($trailer['pubdate'], $xml);
        $this->assertStringContainsString($trailer['url'], $xml);
    }

    public function testRendersRssGuidTag(): void
    {
        $data = [
            'value' => '917393e3-1b1e-5cef-ace4-edaa54e1f810',
        ];
        $this->validWriter->setPodcastIndexGuid($data);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:guid', $xml);
        $this->assertStringContainsString($data['value'], $xml);
    }

    public function testRendersRssMediumTag(): void
    {
        $data = [
            'value' => 'audiobook',
        ];
        $this->validWriter->setPodcastIndexMedium($data);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:medium', $xml);
        $this->assertStringContainsString($data['value'], $xml);
    }
}
