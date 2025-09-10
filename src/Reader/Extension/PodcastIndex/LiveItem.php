<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Extension\PodcastIndex;

use DOMElement;
use Laminas\Feed\Reader\Extension;
use Laminas\Feed\Reader\Entry\Rss;
use stdClass;

use function array_key_exists;
use function assert;

/**
 * Describes PodcastIndex LiveItem data in a RSS Feed
 */
class LiveItem extends Rss
{
    /**
     * Get live item content link
     */
    protected function getContentLink()
    {
        // TODO
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
