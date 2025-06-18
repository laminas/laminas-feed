<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Writer\Extension\PodcastIndex;

use Laminas\Feed\Writer;
use PHPUnit\Framework\TestCase;

use function implode;
use function in_array;
use function time;

class FeedTest extends TestCase
{
    public function testSetLocked(): void
    {
        $feed = new Writer\Feed();

        $locked = [
            'value' => 'yes',
            'owner' => 'john.doe@example.com',
        ];
        $feed->setPodcastIndexLocked($locked);
        $this->assertEquals($locked, $feed->getPodcastIndexLocked());
    }

    public function testSetLockedThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $locked = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLocked($locked);
    }

    /**
     * @psalm-return array<string, array{0: mixed}>
     */
    public static function nonAlphaValues(): array
    {
        return [
            'null'       => [null],
            'zero'       => [0],
            'int'        => [1],
            'zero-float' => [0.0],
            'float'      => [1.1],
            'string'     => ['1'],
            'array'      => [['yes']],
            'object'     => [(object) ['value' => 'yes']],
        ];
    }

    /**
     * @dataProvider nonAlphaValues
     * @param mixed $value
     */
    public function testSetLockedThrowsExceptionOnNonAlphaValue($value): void
    {
        $feed = new Writer\Feed();

        $locked = [
            'value' => $value,
            'owner' => 'john.doe@example.com',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLocked($locked);
    }

    public function testSetFunding(): void
    {
        $feed = new Writer\Feed();

        $funding = [
            'title' => 'Support the show!',
            'url'   => 'http://example.com/donate',
        ];
        $feed->setPodcastIndexFunding($funding);
        $this->assertEquals($funding, $feed->getPodcastIndexFunding());
    }

    public function testSetFundingThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $locked = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexFunding($locked);
    }

    public function testSetLicense(): void
    {
        $feed = new Writer\Feed();

        $license = [
            'identifier' => 'cc-by-4.0',
            'url'        => 'https://spdx.org/licenses/CC-BY-4.0.html',
        ];
        $feed->setPodcastIndexLicense($license);
        $this->assertEquals($license, $feed->getPodcastIndexLicense());
    }

    public function testSetLicenseThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $license = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLicense($license);
    }

    public function testSetLicenseThrowsExceptionOnInvalidIdentifier(): void
    {
        $feed = new Writer\Feed();

        $license = [
            'identifier' => 1234,
            'url'        => 'https://spdx.org/licenses/CC-BY-4.0.html',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLicense($license);
    }

    public function testSetLicenseThrowsExceptionOnInvalidUrl(): void
    {
        $feed = new Writer\Feed();

        $license = [
            'identifier' => 'cc-by-4.0',
            'url'        => 'spdx.org/licenses/CC-BY-4.0.html',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLicense($license);
    }

    public function testSetLicenseThrowsExceptionOnMissingUrl(): void
    {
        $feed = new Writer\Feed();

        $license = [
            'identifier' => 'cc-by-4.0',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLicense($license);
    }

    public function testSetLocation(): void
    {
        $feed = new Writer\Feed();

        $location = [
            'description' => 'London, Baker Street',
            'geo'         => 'geo:-27.86159,153.3169',
            'osm'         => 'W43678282',
        ];
        $feed->setPodcastIndexLocation($location);
        $this->assertEquals($location, $feed->getPodcastIndexLocation());
    }

    public function testSetLocationWithOneArgument(): void
    {
        $feed = new Writer\Feed();

        $location = [
            'description' => 'London, Baker Street',
        ];
        $feed->setPodcastIndexLocation($location);
        $this->assertEquals($location, $feed->getPodcastIndexLocation());
    }

    public function testSetLocationThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $location = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLocation($location);
    }

    public function testSetLocationThrowsExceptionOnInvalidGeo(): void
    {
        $feed = new Writer\Feed();

        $location = [
            'description' => 'London, Baker Street',
            'geo'         => [-27.86159, 153.3169],
            'osm'         => 'W43678282',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLocation($location);
    }

    public function testSetLocationThrowsExceptionOnInvalidOsm(): void
    {
        $feed = new Writer\Feed();

        $location = [
            'description' => 'London, Baker Street',
            'geo'         => 'geo:-27.86159,153.3169',
            'osm'         => false,
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexLocation($location);
    }

    public function testSetImages(): void
    {
        $feed = new Writer\Feed();

        $srcset = [
            "https://example.com/images/ep1/pci_avatar-massive.jpg 1500w",
            "https://example.com/images/ep1/pci_avatar-middle.jpg 600w",
            "https://example.com/images/ep1/pci_avatar-small.jpg 300w",
            "https://example.com/images/ep1/pci_avatar-tiny.jpg 150w",
        ];
        $images = [
            'srcset' => implode(", ", $srcset), // cast to string
        ];

        $feed->setPodcastIndexImages($images);
        $this->assertEquals($images, $feed->getPodcastIndexImages());
    }

    public function testSetImagesThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $images = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexImages($images);
    }

    public function testSetImagesThrowsExceptionOnInvalidSrcsetType(): void
    {
        $feed = new Writer\Feed();

        $srcset = [
            "https://example.com/images/ep1/pci_avatar-massive.jpg 1500w",
            "https://example.com/images/ep1/pci_avatar-middle.jpg 600w",
            "https://example.com/images/ep1/pci_avatar-small.jpg 300w",
            "https://example.com/images/ep1/pci_avatar-tiny.jpg 150w",
        ];
        $images = [
            'srcset' => $srcset, // plain array, not allowed
        ];

        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexImages($images);
    }

    public function testSetUpdateFrequency(): void
    {
        $feed = new Writer\Feed();

        $updateFrequency = [
            'description' => 'Daily',
            'complete'    => false,
            'dtstart'     => '2023-08-28T00:00:00.000Z',
            'rrule'       => 'FREQ=DAILY',
        ];
        $feed->setPodcastIndexUpdateFrequency($updateFrequency);
        $this->assertEquals($updateFrequency, $feed->getPodcastIndexUpdateFrequency());
    }

    public function testSetUpdateFrequencyWithOneArgument(): void
    {
        $feed = new Writer\Feed();

        $updateFrequency = [
            'description' => 'Daily',
        ];
        $feed->setPodcastIndexUpdateFrequency($updateFrequency);
        $this->assertEquals($updateFrequency, $feed->getPodcastIndexUpdateFrequency());
    }

    public function testSetUpdateFrequencyThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $updateFrequency = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexUpdateFrequency($updateFrequency);
    }

    public function testSetUpdateFrequencyThrowsExceptionOnInvalidCompleteValue(): void
    {
        $feed = new Writer\Feed();

        $updateFrequency = [
            'description' => 'Daily',
            'complete'    => 'yes',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexUpdateFrequency($updateFrequency);
    }

    public function testSetUpdateFrequencyThrowsExceptionOnInvalidDateValue(): void
    {
        $feed = new Writer\Feed();

        $updateFrequency = [
            'description' => 'Daily',
            'dtstart'     => time(),
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->setPodcastIndexUpdateFrequency($updateFrequency);
    }

    public function testAddPerson(): void
    {
        $feed = new Writer\Feed();

        $person = [
            'name'  => 'Hercules Poirot',
            'role'  => 'guest',
            'group' => 'starring',
            'img'   => 'https://poirot.com/about/my-moustage.jpg',
            'href'  => 'https://poirot.com/my-cases',
        ];
        $feed->addPodcastIndexPerson($person);
        $this->assertTrue(in_array($person, $feed->getPodcastIndexPersons()));
    }

    public function testSetPersons(): void
    {
        $feed = new Writer\Feed();

        $persons = [
            [
                'name'  => 'Hercules Poirot',
                'role'  => 'guest',
                'group' => 'starring',
                'img'   => 'https://poirot.com/about/my-moustage.jpg',
                'href'  => 'https://poirot.com/my-cases',
            ],
            [
                'name'  => 'Agatha Christie',
                'role'  => 'guest',
                'group' => 'writing',
            ],
        ];
        // set
        $feed->setPodcastIndexPersons($persons);
        foreach ($persons as $person) {
            $this->assertTrue(in_array($person, $feed->getPodcastIndexPersons()));
        }
        // delete
        $feed->setPodcastIndexPersons();
        $this->assertNull($feed->getPodcastIndexPersons());
    }

    public function testSetPersonWithOneArgument(): void
    {
        $feed = new Writer\Feed();

        $person = [
            'name' => 'Hercules Poirot',
        ];
        $feed->addPodcastIndexPerson($person);
        $this->assertTrue(in_array($person, $feed->getPodcastIndexPersons()));
    }

    public function testSetPersonThrowsExceptionOnInvalidArguments(): void
    {
        $feed = new Writer\Feed();

        $person = [
            'abc' => 'def',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->addPodcastIndexPerson($person);
    }

    public function testSetPersonThrowsExceptionOnInvalidImageUrl(): void
    {
        $feed = new Writer\Feed();

        $person = [
            'name'  => 'Hercules Poirot',
            'role'  => 'guest',
            'group' => 'writing',
            'img'   => 'poirot.com/my-moustage.jpg',
        ];
        $this->expectException(Writer\Exception\InvalidArgumentException::class);
        $feed->addPodcastIndexPerson($person);
    }
}
