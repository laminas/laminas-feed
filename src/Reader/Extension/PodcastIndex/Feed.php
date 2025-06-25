<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Extension\PodcastIndex;

// phpcs:disable SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use DateTimeInterface;
// phpcs:enable SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use DOMElement;
use Laminas\Feed\Reader\Extension;
use stdClass;

use function array_key_exists;
use function assert;

/**
 * Describes PodcastIndex data of a RSS Feed
 *
 * @psalm-type UpdateFrequencyObject = object{
 *     description: string,
 *     complete?: bool,
 *     dtstart?: DateTimeInterface,
 *     rrule?: string
 *     }
 * @psalm-type PersonObject = object{
 *         name: string,
 *         role?: string,
 *         group?: string,
 *         img?: string,
 *         href?: string
 *  }
 */
class Feed extends Extension\AbstractFeed
{
    /**
     * Is the podcast locked (not available for indexing)?
     */
    public function isLocked(): bool
    {
        if (isset($this->data['locked'])) {
            return $this->data['locked'];
        }

        $locked = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/podcast:locked)');

        if (! $locked) {
            $locked = false;
        }

        $this->data['locked'] = $locked === 'yes';

        return $this->data['locked'];
    }

    /**
     * Get the owner of the podcast (for verification)
     */
    public function getLockOwner(): ?string
    {
        return $this->getPodcastIndexLockOwner();
    }

    /**
     * Get the owner of the podcast (for verification)
     */
    public function getPodcastIndexLockOwner(): ?string
    {
        if (isset($this->data['owner'])) {
            return $this->data['owner'];
        }

        $owner = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/podcast:locked/@owner)');

        if (! $owner) {
            $owner = null;
        }

        $this->data['owner'] = $owner;

        return $this->data['owner'];
    }

    /**
     * Get the entry funding link
     */
    public function getFunding(): ?stdClass
    {
        return $this->getPodcastIndexFunding();
    }

    /**
     * Get the entry funding link
     */
    public function getPodcastIndexFunding(): ?stdClass
    {
        if (array_key_exists('funding', $this->data)) {
            /** @var stdClass $object */
            $object = $this->data['funding'];
            return $object;
        }

        $funding = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:funding');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $funding        = new stdClass();
            $funding->url   = $item->getAttribute('url');
            $funding->title = $item->nodeValue;
        }

        $this->data['funding'] = $funding;

        return $this->data['funding'];
    }

    /**
     * Get the podcast license
     *
     * @return null|object{identifier: string, url: string}
     */
    public function getPodcastIndexLicense(): object|null
    {
        if (array_key_exists('license', $this->data)) {
            /** @var null|object{identifier: string, url: string} $object */
            $object = $this->data['license'];
            return $object;
        }

        $license = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:license');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $license             = new stdClass();
            $license->identifier = $item->nodeValue;
            $license->url        = $item->getAttribute('url');
        }

        $this->data['license'] = $license;

        return $this->data['license'];
    }

    /**
     * Get the podcast location
     *
     * @return null|object{description: string, geo?: string, osm?: string}
     */
    public function getPodcastIndexLocation(): object|null
    {
        if (array_key_exists('location', $this->data)) {
            /** @var null|object{description: string, geo?: string, osm?: string} $object */
            $object = $this->data['location'];
            return $object;
        }

        $location = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:location');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $location              = new stdClass();
            $location->description = $item->nodeValue;
            $location->geo         = $item->getAttribute('geo');
            $location->osm         = $item->getAttribute('osm');
        }

        $this->data['location'] = $location;

        return $this->data['location'];
    }

    /**
     * Get the podcast images
     *
     * @return null|object{srcset: string}
     */
    public function getPodcastIndexImages(): object|null
    {
        if (array_key_exists('images', $this->data)) {
            /** @var null|object{srcset: string} $object */
            $object = $this->data['images'];
            return $object;
        }

        $images = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:images');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $images         = new stdClass();
            $images->srcset = $item->getAttribute('srcset');
        }

        $this->data['images'] = $images;

        return $this->data['images'];
    }

    /**
     * Get the podcast update frequency
     *
     * @psalm-return null|UpdateFrequencyObject
     */
    public function getPodcastIndexUpdateFrequency(): object|null
    {
        if (array_key_exists('updateFrequency', $this->data)) {
            /** @var null|UpdateFrequencyObject $object */
            $object = $this->data['updateFrequency'];
            return $object;
        }

        $updateFrequency = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:updateFrequency');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $updateFrequency              = new stdClass();
            $updateFrequency->description = $item->nodeValue;
            $updateFrequency->complete    = $item->getAttribute('complete');
            $updateFrequency->dtstart     = $item->getAttribute('dtstart');
            $updateFrequency->rrule       = $item->getAttribute('rrule');
        }

        $this->data['updateFrequency'] = $updateFrequency;

        return $this->data['updateFrequency'];
    }

    /**
     * Get the podcast people
     *
     * @psalm-return list<PersonObject>
     */
    public function getPodcastIndexPeople(): array
    {
        if (array_key_exists('people', $this->data)) {
            /** @var list<PersonObject> $people */
            $people = $this->data['people'];
            return $people;
        }

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:person');

        $personCollection = [];

        if ($nodeList->length) {
            foreach ($nodeList as $entry) {
                assert($entry instanceof DOMElement);
                $person        = new stdClass();
                $person->name  = $entry->nodeValue;
                $person->role  = $entry->getAttribute('role');
                $person->group = $entry->getAttribute('group');
                $person->img   = $entry->getAttribute('img');
                $person->href  = $entry->getAttribute('href');

                $personCollection[] = $person;
            }
        }

        $this->data['people'] = $personCollection;

        return $this->data['people'];
    }

    /**
     * Register PodcastIndex namespace
     */
    protected function registerNamespaces(): void
    {
        $this->xpath->registerNamespace(
            'podcast',
            'https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/1.0.md'
        );
    }
}
