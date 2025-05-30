<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Writer\Renderer\Extension\PodcastIndex;

use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Renderer;
use PHPUnit\Framework\TestCase;

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
        $images = [
            'srcset' => 'London, Baker Street',
        ];
        $this->validWriter->setPodcastIndexImages($images);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:images', $xml);
        $this->assertStringContainsString($images['srcset'], $xml);
    }
}
