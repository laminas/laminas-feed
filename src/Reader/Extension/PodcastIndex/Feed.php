<?php

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
