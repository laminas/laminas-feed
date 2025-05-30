<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Extension\PodcastIndex;

use Laminas\Feed\Reader\Extension;
use stdClass;

use function array_key_exists;

/**
 * Describes PodcastIndex data of a RSS Feed
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
     *
     * @psalm-return null|object{url: string, title: string}
     */
    public function getFunding(): ?stdClass
    {
        if (array_key_exists('funding', $this->data)) {
            return $this->data['funding'];
        }

        $funding = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:funding');

        if ($nodeList->length > 0) {
            $funding        = new stdClass();
            $funding->url   = $nodeList->item(0)->getAttribute('url');
            $funding->title = $nodeList->item(0)->nodeValue;
        }

        $this->data['funding'] = $funding;

        return $this->data['funding'];
    }

    /**
     * Get the podcast license
     *
     * @psalm-return null|object{identifier: string, url: string}
     */
    public function getLicense(): ?stdClass
    {
        if (array_key_exists('license', $this->data)) {
            return $this->data['license'];
        }

        $license = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:license');

        if ($nodeList->length > 0) {
            $license             = new stdClass();
            $license->identifier = $nodeList->item(0)->nodeValue;
            $license->url        = $nodeList->item(0)->getAttribute('url');
        }

        $this->data['license'] = $license;

        return $this->data['license'];
    }

    /**
     * Get the podcast location
     *
     * @psalm-return null|object{text: string, geo: string, osm: string}
     */
    public function getLocation(): ?stdClass
    {
        if (array_key_exists('location', $this->data)) {
            return $this->data['location'];
        }

        $location = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:location');

        if ($nodeList->length > 0) {
            $location              = new stdClass();
            $location->description = $nodeList->item(0)->nodeValue;
            $location->geo         = $nodeList->item(0)->getAttribute('geo');
            $location->osm         = $nodeList->item(0)->getAttribute('osm');
        }

        $this->data['location'] = $location;

        return $this->data['location'];
    }

    /**
     * Get the podcast images
     *
     * @psalm-return null|object{scrset: string}
     */
    public function getImages(): ?stdClass
    {
        if (array_key_exists('images', $this->data)) {
            return $this->data['images'];
        }

        $images = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:images');

        if ($nodeList->length > 0) {
            $images         = new stdClass();
            $images->srcset = $nodeList->item(0)->getAttribute('srcset');
        }

        $this->data['images'] = $images;

        return $this->data['images'];
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
