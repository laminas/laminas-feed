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
 *     name: string,
 *     role?: string,
 *     group?: string,
 *     img?: string,
 *     href?: string
 *  }
 * @psalm-type TrailerObject = object{
 *     title: string,
 *     pubdate: string,
 *     url: string,
 *     length?: int,
 *     type?: string,
 *     season?: int
 * }
 */
class Feed extends Extension\AbstractFeed
{
    /**
     * Is the podcast locked (not available for indexing)?
     */
    public function isLocked(): bool
    {
        return $this->isPodcastIndexLocked();
    }

    /**
     * Is the podcast locked (not available for indexing)?
     */
    public function isPodcastIndexLocked(): bool
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
            /** @psalm-var stdClass */
            return $this->data['funding'];
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
            /** @psalm-var null|object{identifier: string, url: string} */
            return $this->data['license'];
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
            /** @psalm-var null|object{description: string, geo?: string, osm?: string} */
            return $this->data['location'];
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
            /** @psalm-var null|object{srcset: string} */
            return $this->data['images'];
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
            /** @psalm-var null|UpdateFrequencyObject */
            return $this->data['updateFrequency'];
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
            /** @psalm-var list<PersonObject> */
            return $this->data['people'];
        }

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:person');

        $personCollection = [];

        if ($nodeList->length > 0) {
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
     * Get the podcast trailer
     *
     * @return null|TrailerObject
     */
    public function getPodcastIndexTrailer(): object|null
    {
        if (array_key_exists('trailer', $this->data)) {
            /** @psalm-var null|TrailerObject */
            return $this->data['trailer'];
        }

        $object = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:trailer');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $object          = new stdClass();
            $object->title   = $item->nodeValue;
            $object->pubdate = $item->getAttribute('pubdate');
            $object->url     = $item->getAttribute('url');
            $object->length  = $item->getAttribute('length');
            $object->type    = $item->getAttribute('type');
            $object->season  = $item->getAttribute('season');
        }

        $this->data['trailer'] = $object;

        return $this->data['trailer'];
    }

    /**
     * Get the podcast guid
     *
     * @return null|object{value: string}
     */
    public function getPodcastIndexGuid(): object|null
    {
        if (array_key_exists('guid', $this->data)) {
            /** @psalm-var null|object{value: string} */
            return $this->data['guid'];
        }

        $object = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:guid');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $object        = new stdClass();
            $object->value = $item->nodeValue;
        }

        $this->data['guid'] = $object;

        return $this->data['guid'];
    }

    /**
     * Get the podcast medium
     *
     * @return null|object{value: string}
     */
    public function getPodcastIndexMedium(): object|null
    {
        if (array_key_exists('medium', $this->data)) {
            /** @psalm-var null|object{value: string} */
            return $this->data['medium'];
        }

        $object = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:medium');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $object        = new stdClass();
            $object->value = $item->nodeValue;
        }

        $this->data['medium'] = $object;

        return $this->data['medium'];
    }

    /**
     * Get the podcast blocks
     *
     * @return list<object{value: string, id?: string}>
     */
    public function getPodcastIndexBlocks(): array
    {
        if (array_key_exists('blocks', $this->data)) {
            /** @psalm-var list<object{value: string, id?: string}> */
            return $this->data['blocks'];
        }

        $blocks = [];

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:block');

        foreach ($nodeList as $entry) {
            assert($entry instanceof DOMElement);
            $object        = new stdClass();
            $object->value = $entry->nodeValue;
            $object->id    = $entry->getAttribute('id');

            $blocks[] = $object;
        }

        $this->data['blocks'] = $blocks;

        return $this->data['blocks'];
    }

    /**
     * Get the podcast txts
     *
     * @return list<object{value: string, purpose?: string}>
     */
    public function getPodcastIndexTxts(): array
    {
        if (array_key_exists('txts', $this->data)) {
            /** @psalm-var list<object{value: string, purpose?: string}> */
            return $this->data['txts'];
        }

        $txts = [];

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:txt');

        foreach ($nodeList as $entry) {
            assert($entry instanceof DOMElement);
            $object          = new stdClass();
            $object->value   = $entry->nodeValue;
            $object->purpose = $entry->getAttribute('purpose');

            $txts[] = $object;
        }

        $this->data['txts'] = $txts;

        return $this->data['txts'];
    }

    /**
     * Get the podcast podping
     *
     * @return null|object{usesPodping: bool}
     */
    public function getPodcastIndexPodping(): object|null
    {
        if (array_key_exists('podping', $this->data)) {
            /** @psalm-var null|object{usesPodping: bool} */
            return $this->data['podping'];
        }

        $object = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:podping');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);

            $object = new stdClass();

            $object->usesPodping = $item->getAttribute('usesPodping') === 'true';
        }

        $this->data['podping'] = $object;

        return $this->data['podping'];
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
