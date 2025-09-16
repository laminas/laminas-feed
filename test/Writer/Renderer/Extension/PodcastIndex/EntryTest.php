<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Writer\Renderer\Extension\PodcastIndex;

use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Renderer;
use PHPUnit\Framework\TestCase;

use function is_string;
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

    public function testRendersRssValueTagsWithRecipientsAndTimeSplits(): void
    {
        $value = [
            'type'      => "lightning",
            'method'    => "keysend",
            'suggested' => 0.00000005000,
        ];

        $valueRecipients = [
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

        $timeSplitRecipients = [
            [
                'name'        => "Alice (Podcaster)",
                'type'        => "node",
                'address'     => "02d5c1bf8b940dc9cadca86d1b0a3c37fbe39cee4c7e839e33bef9174531d27f52",
                'split'       => 40,
                'customKey'   => "Some_custom_key",
                'customValue' => "Some_custom_value",
                'fee'         => true,
            ],
            [
                'name'    => "Malcolm (Guest)",
                'type'    => "node",
                'address' => "02dd306e68c46681aa21d88a436fb35355a8579dd30201581cefa17cb179fc4c15",
                'split'   => 20,
                'fee'     => true,
            ],
        ];
        $valueTimeSplits     = [
            [
                'startTime'       => 63,
                'duration'        => 388,
                'valueRecipients' => $timeSplitRecipients,
            ],
        ];

        $this->validEntry->addPodcastIndexValue($value, $valueRecipients, $valueTimeSplits);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:value', $xml);
        $this->assertStringContainsString('<podcast:valueRecipient', $xml);
        $this->assertStringContainsString('<podcast:valueTimeSplit', $xml);

        $this->assertStringContainsString($value['type'], $xml);
        $this->assertStringContainsString($value['method'], $xml);
        $this->assertStringContainsString($valueRecipients[0]['name'], $xml);
        $this->assertStringContainsString($valueRecipients[0]['type'], $xml);
        $this->assertStringContainsString($valueRecipients[1]['address'], $xml);
        $this->assertStringContainsString((string) $valueRecipients[1]['split'], $xml);
        $this->assertStringContainsString((string) $valueTimeSplits[0]['startTime'], $xml);
        $this->assertStringContainsString((string) $valueTimeSplits[0]['duration'], $xml);
        $this->assertStringContainsString($timeSplitRecipients[0]['name'], $xml);
        $this->assertStringContainsString($timeSplitRecipients[0]['type'], $xml);
        $this->assertStringContainsString($timeSplitRecipients[0]['address'], $xml);
        $this->assertStringContainsString((string) $timeSplitRecipients[1]['split'], $xml);
        $this->assertStringContainsString('fee="true"', $xml);

        $newValue             = [
            'type'      => "lightning",
            'method'    => "keysend",
            'suggested' => 0.00000005000,
        ];
        $newRecipients        = [
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
        $timeSplitRemoteItems = [
            [
                'itemGuid' => "https://podcastindex.org/podcast/4148683#1",
                'feedGuid' => "a94f5cc9-8c58-55fc-91fe-a324087a655b",
                'medium'   => "music",
            ],
            [
                'itemGuid' => "https://podcastindex.org/podcast/4148683#3",
                'feedGuid' => "b83f5cc9-8c58-55fc-91fe-a324087a644c",
                'medium'   => "podcast",
                'feedUrl'  => "https://podcastindex.org/podcast/4148683",
                'title'    => "My Fancy Podcast",
            ],
        ];
        $valueTimeSplits      = [
            [
                'startTime'  => 82,
                'duration'   => 200,
                'remoteItem' => $timeSplitRemoteItems[0],
            ],
            [
                'startTime'  => 134,
                'duration'   => 123,
                'remoteItem' => $timeSplitRemoteItems[1],
            ],
        ];

        $this->validEntry->addPodcastIndexValue($newValue, $newRecipients, $valueTimeSplits);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString(number_format($newValue['suggested'], 11), $xml);
        $this->assertStringContainsString($newRecipients[0]['name'], $xml);
        $this->assertStringContainsString((string) $newRecipients[1]['split'], $xml);
        $this->assertStringContainsString((string) $valueTimeSplits[0]['startTime'], $xml);
        $this->assertStringContainsString((string) $valueTimeSplits[0]['duration'], $xml);
        $this->assertStringContainsString((string) $valueTimeSplits[1]['startTime'], $xml);
        $this->assertStringContainsString((string) $valueTimeSplits[1]['duration'], $xml);
        $this->assertStringContainsString($timeSplitRemoteItems[0]['itemGuid'], $xml);
        $this->assertStringContainsString($timeSplitRemoteItems[0]['feedGuid'], $xml);
        $this->assertStringContainsString($timeSplitRemoteItems[0]['medium'], $xml);
        $this->assertStringContainsString($timeSplitRemoteItems[1]['itemGuid'], $xml);
        $this->assertStringContainsString($timeSplitRemoteItems[1]['feedGuid'], $xml);
        $this->assertStringContainsString($timeSplitRemoteItems[1]['medium'], $xml);
        $this->assertStringContainsString($timeSplitRemoteItems[1]['itemGuid'], $xml);
        $this->assertStringContainsString($timeSplitRemoteItems[1]['feedUrl'], $xml);
        $this->assertStringContainsString($timeSplitRemoteItems[1]['title'], $xml);
    }

    public function testRendersRssSeasonTag(): void
    {
        $season = [
            'value' => 3,
            'name'  => 'The Yearling - Chapter 3',
        ];
        $this->validEntry->setPodcastIndexSeason($season);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:season name="The Yearling - Chapter 3">3</podcast:season>', $xml);
    }

    public function testRendersRssEpisodeTag(): void
    {
        $episode = [
            'value'   => 9,
            'display' => 'Day 5',
        ];
        $this->validEntry->setPodcastIndexEpisode($episode);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:episode display="Day 5">9</podcast:episode>', $xml);
    }

    public function testRendersRssAlternateEnclosureTag(): void
    {
        $sources            = [
            [
                'uri'         => 'https://example.com/file-720.torrent',
                'contentType' => 'application/x-bittorrent',
            ],
            [
                'uri' => 'ipfs://QmX33FYehk6ckGQ6g1D9D3FqZPix5JpKstKQKbaS8quUFb',
            ],
        ];
        $integrity          = [
            'type'  => 'sri',
            'value' => 'sha384-ExVqijgYHm15PqQqdXfW95x+Rs6C+d6E/ICxyQOeFevnxNLR/wtJNrNYTjIysUBo',
        ];
        $alternateEnclosure = [
            'type'    => 'video/mp4',
            'length'  => 7924786,
            'bitrate' => 511276.52,
            'height'  => 720,
            'lang'    => 'en',
            'title'   => 'Standard',
            'rel'     => 'default',
            'codecs'  => 'avc1.42E01E, mp4a.40.2',
            'default' => true,
        ];

        $this->validEntry->addPodcastIndexAlternateEnclosure($alternateEnclosure, $sources, $integrity);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:alternateEnclosure', $xml);

        foreach ($alternateEnclosure as $att) {
            $att = ! is_string($att) ? (string) $att : $att;
            $this->assertStringContainsString($att, $xml);
        }
        foreach ($sources[0] as $att) {
            $this->assertStringContainsString($att, $xml);
        }
        foreach ($sources[1] as $att) {
            $this->assertStringContainsString($att, $xml);
        }
        foreach ($integrity as $att) {
            $this->assertStringContainsString($att, $xml);
        }
    }

    public function testRendersRssAlternateEnclosureMinimal(): void
    {
        $sources            = [
            [
                'uri' => 'ipfs://QmX33FYehk6ckGQ6g1D9D3FqZPix5JpKstKQKbaS8quUFb',
            ],
        ];
        $alternateEnclosure = [
            'type' => 'video/mp4',
        ];

        $this->validEntry->addPodcastIndexAlternateEnclosure($alternateEnclosure, $sources);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $parent = '<podcast:alternateEnclosure type="video/mp4">';
        $child  = '<podcast:source uri="ipfs://QmX33FYehk6ckGQ6g1D9D3FqZPix5JpKstKQKbaS8quUFb"/>';
        $this->assertStringContainsString($parent, $xml);
        $this->assertStringContainsString($child, $xml);
    }

    public function testRendersRssDetailedImageTag(): void
    {
        $images = [
            [
                'alt'         => "An antenna emanating signal waves",
                'purpose'     => "artwork",
                'type'        => "image/jpeg",
                'aspectRatio' => "1/1",
                'href'        => "https://example.com/images/ep1/pci_square-massive.jpg",
                'width'       => 1400,
                'height'      => 1400,
            ],
            [
                'alt'         => "Another antenna emanating signal waves",
                'purpose'     => "artwork social",
                'type'        => "image/jpeg",
                'aspectRatio' => "16/9",
                'href'        => "https://example.com/images/ep1/pci_landscape-massive_wide.jpg",
            ],
        ];

        $this->validEntry->setPodcastIndexDetailedImages($images);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $this->assertStringContainsString('<podcast:image', $xml);

        foreach ($images as $image) {
            foreach ($image as $attribute) {
                $attribute = is_string($attribute) ? $attribute : (string) $attribute;
                $this->assertStringContainsString($attribute, $xml);
            }
        }
    }

    public function testRendersRssContentLinkTag(): void
    {
        $data = [
            [
                'href'        => 'https://youtube.com/pc20/livestream',
                'description' => 'YouTube!',
            ],
            [
                'href'        => 'https://twitch.com/pc20/livestream',
                'description' => 'Twitch!',
            ],
        ];

        $this->validEntry->setPodcastIndexContentLinks($data);

        $rssFeed = new Renderer\Feed\Rss($this->validWriter);
        $xml     = $rssFeed->render()->saveXml();

        $expected1 = '<podcast:contentLink href="https://youtube.com/pc20/livestream">YouTube!</podcast:contentLink>';
        $expected2 = '<podcast:contentLink href="https://twitch.com/pc20/livestream">Twitch!</podcast:contentLink>';
        $this->assertStringContainsString($expected1, $xml);
        $this->assertStringContainsString($expected2, $xml);
    }
}
