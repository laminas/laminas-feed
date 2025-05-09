<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Writer\Renderer\Extension\PodcastIndex;

use Laminas\Feed\Writer;
use Laminas\Feed\Reader;
use PHPUnit\Framework\TestCase;
use Laminas\Feed\Writer\Renderer;
class FeedTest extends TestCase
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
    }

    public function testRendersLockedTag(): void
    {
        $locked = [
            'value' => 'yes',
            'owner' => 'john.doe@example.com',
        ];
        $this->validWriter->setPodcastIndexLocked($locked);
        
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $xml = $feed->getDomDocument()->saveXml();

        $this->assertStringContainsString('<podcast:locked', $xml);
    }

    public function testRendersFundingTag(): void
    {
        $funding = [
            'title' => 'Support the show!',
            'url'   => 'http://example.com/donate',
        ];
        $this->validWriter->setPodcastIndexFunding($funding);
        
        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $rssFeed->render();
        $feed = Reader\Reader::importString($rssFeed->saveXml());
        $xml = $feed->getDomDocument()->saveXml();

        $this->assertStringContainsString('<podcast:funding', $xml);
    }
}
