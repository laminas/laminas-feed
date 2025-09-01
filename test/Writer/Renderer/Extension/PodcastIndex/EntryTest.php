<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Writer\Renderer\Extension\PodcastIndex;

use DateTime;
use DateTimeInterface;
use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Renderer;
use PHPUnit\Framework\TestCase;

use function implode;
use function number_format;
use function substr_count;

class EntryTest extends TestCase
{
    protected Writer\Feed $validWriter;
    protected Writer\Entry $validEntry;

    protected function setUp(): void
    {
        Writer\Writer::reset();

        $this->validWriter = new Writer\Feed();
        $this->validWriter->setTitle('This is a test feed.');
        $this->validWriter->setDescription('This is a test description.');
        $this->validWriter->setLink('http://www.example.com');
        $this->validWriter->setType('rss');

        $this->validEntry = $this->validWriter->createEntry();
        $this->validEntry->setTitle('This is a test entry.');
        $this->validEntry->setDateModified(1_234_567_890);
        $this->validEntry->setDateCreated(1_234_567_000);
        $this->validEntry->setLink('http://www.example.com/1');
        $this->validEntry->addAuthor([
            'name'  => 'Jane',
            'email' => 'jane@example.com',
            'uri'   => 'http://www.example.com/jane',
        ]);

        $this->validWriter->addEntry($this->validEntry);
    }

    protected function tearDown(): void
    {
        Writer\Writer::reset();
    }

    public function testRendersRssLocationTag(): void
    {
        $location = [
            'description' => 'London, Baker Street',
            'geo'         => 'geo:-27.86159,153.3169',
            'osm'         => 'W43678282',
            'rel'         => 'creator',
            'country'     => 'GB',
        ];
        $this->validEntry->setPodcastIndexLocation($location);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:location', $xml);
        $this->assertStringContainsString($location['description'], $xml);
        $this->assertStringContainsString($location['geo'], $xml);
        $this->assertStringContainsString($location['osm'], $xml);
        $this->assertStringContainsString($location['rel'], $xml);
        $this->assertStringContainsString($location['country'], $xml);
    }

    public function testRendersRssLicenseTag(): void
    {
        $identifier = 'cc-by-4.0';
        $url        = 'https://spdx.org/licenses/CC-BY-4.0.html';

        $license = [
            'identifier' => $identifier,
            'url'        => $url,
        ];
        $this->validEntry->setPodcastIndexLicense($license);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:license', $xml);
        $this->assertStringContainsString($url, $xml);
        $this->assertStringContainsString($identifier, $xml);
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

        $this->validEntry->addPodcastIndexPerson($person);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:person', $xml);
        $this->assertStringContainsString($person['name'], $xml);
        $this->assertSame(1, substr_count($xml, $person['name']));
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

        $this->validEntry->setPodcastIndexPeople($people);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString(">$fName</podcast:person>", $xml);
        $this->assertStringContainsString(">$sName</podcast:person>", $xml);
    }

    public function testRendersMultipleRssPersonTagsUsingAlias(): void
    {
        $fName = 'Hercules Poirot';
        $sName = 'Agatha Christie';

        $people = [
            ['name' => $fName],
            ['name' => $sName],
        ];

        $this->validEntry->setPodcastIndexPersons($people);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString(">$fName</podcast:person>", $xml);
        $this->assertStringContainsString(">$sName</podcast:person>", $xml);
    }

    public function testRendersRssTxtTag(): void
    {
        $data = [
            'value'   => 'S6lpp-7ZCn8-dZfGc-OoyaG',
            'purpose' => 'verify',
        ];

        $this->validEntry->addPodcastIndexTxt($data);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:txt', $xml);
        $this->assertStringContainsString($data['value'], $xml);
        $this->assertStringContainsString($data['purpose'], $xml);
        $this->assertSame(1, substr_count($xml, $data['purpose']));
    }

    public function testRendersMultipleRssTxtTags(): void
    {
        $data = [
            [
                'value'   => 'S6lpp-7ZCn8-dZfGc-OoyaG',
                'purpose' => 'verify',
            ],
            [
                'value' => '2022-10-26T04:45:30.742Z',
            ],
        ];

        $this->validEntry->setPodcastIndexTxts($data);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:txt purpose="verify">S6lpp-7ZCn8-dZfGc-OoyaG</podcast:txt>', $xml);
        $this->assertStringContainsString('<podcast:txt>2022-10-26T04:45:30.742Z</podcast:txt>', $xml);
    }

    public function testRendersRssSocialInteractTags(): void
    {
        $data = [
            [
                'priority'   => 1,
                'protocol'   => "activitypub",
                'uri'        => "https://podcastindex.social/web/@dave/108013847520053258",
                'accountId'  => "@dave",
                'accountUrl' => "https://podcastindex.social/web/@dave",
            ],
            [
                'priority'   => 2,
                'protocol'   => "twitter",
                'uri'        => "https://twitter.com/PodcastindexOrg/status/1507120226361647115",
                'accountId'  => "@podcastindexorg",
                'accountUrl' => "https://twitter.com/PodcastindexOrg",
            ],
        ];

        $this->validEntry->setPodcastIndexSocialInteracts($data);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:socialInteract', $xml);
        $this->assertStringContainsString((string) $data[0]['priority'], $xml);
        $this->assertStringContainsString($data[0]['protocol'], $xml);
        $this->assertStringContainsString($data[0]['uri'], $xml);
        $this->assertStringContainsString($data[1]['accountId'], $xml);
        $this->assertStringContainsString($data[1]['accountUrl'], $xml);
    }

    public function testRendersRssValueTagsWithChildren(): void
    {
        $value = [
            'type'      => "lightning",
            'method'    => "keysend",
            'suggested' => 0.00000005000,
        ];

        $recipients = [
            [
                'name'    => "Alice (Podcaster)",
                'type'    => "node",
                'address' => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 40,
            ],
            [
                'name'    => "Bob (Podcaster)",
                'type'    => "node",
                'address' => "032f4ffbbafffbe51726ad3c164a3d0d37ec27bc67b29a159b0f49ae8ac21b8508",
                'split'   => 60,
            ],
        ];
        $this->validEntry->addPodcastIndexValue($value, $recipients);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:value', $xml);
        $this->assertStringContainsString('<podcast:valueRecipient', $xml);
        $this->assertStringContainsString($value['type'], $xml);
        $this->assertStringContainsString($value['method'], $xml);
        $this->assertStringContainsString($recipients[0]['name'], $xml);
        $this->assertStringContainsString($recipients[0]['type'], $xml);
        $this->assertStringContainsString($recipients[1]['address'], $xml);
        $this->assertStringContainsString((string) $recipients[1]['split'], $xml);

        $newValue      = [
            'type'      => "lightning",
            'method'    => "keysend",
            'suggested' => 0.00000005000,
        ];
        $newRecipients = [
            [
                'name'    => "Louis (Podcaster)",
                'type'    => "node",
                'address' => "0345c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'   => 50,
            ],
            [
                'name'    => "Edith (Podcaster)",
                'type'    => "node",
                'address' => "03454ffbbafffbe51726ad3c164a3d0d37ec27bc67b29a159b0f49ae8ac21b8508",
                'split'   => 50,
            ],
        ];

        $this->validEntry->addPodcastIndexValue($newValue, $newRecipients);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString(number_format($newValue['suggested'], 11), $xml);
        $this->assertStringContainsString($newRecipients[0]['name'], $xml);
        $this->assertStringContainsString((string) $newRecipients[1]['split'], $xml);
    }
}
