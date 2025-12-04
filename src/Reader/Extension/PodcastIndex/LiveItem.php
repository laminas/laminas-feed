<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Extension\PodcastIndex;

use DOMAttr;
use DOMElement;
use Laminas\Feed\Reader\Entry\Rss as EntryReader;

use function array_key_exists;
use function assert;

/**
 * Describes PodcastIndex LiveItem data in a RSS Feed
 */
final class LiveItem extends EntryReader
{
    public function __construct(DOMElement $liveItem, string $liveItemKey, ?string $type = null)
    {
        parent::__construct($liveItem, $liveItemKey, $type);

        // override xpath queries to fetch liveItems, not items
        $index               = $this->entryKey + 1;
        $this->xpathQueryRss = '//podcast:liveItem[' . $index . ']';
        $this->xpathQueryRdf = '//podcast:liveItem[' . $index . ']';

        // also ensure that for the PodcastIndex extension entries
        $prefix = $this->xpathQueryRss;
        /** @psalm-var mixed $extension */
        foreach ($this->extensions as $extension) {
            if ($extension instanceof Entry) {
                $extension->setXpathPrefix($prefix);
            }
        }
    }

    public function getStatus(): ?string
    {
        if (array_key_exists('status', $this->data)) {
            /** @psalm-var string */
            return $this->data['status'];
        }

        $status = null;
        /** @var string $prefix*/
        $prefix   = $this->getXpathPrefix();
        $nodeList = $this->xpath->query($prefix . '/@status');

        if ($nodeList->length > 0) {
            $node = $nodeList->item(0);
            assert($node instanceof DOMAttr);
            $status = $node->value;
        }

        $this->data['status'] = $status;
        return $this->data['status'];
    }

    public function getStart(): ?string
    {
        if (array_key_exists('start', $this->data)) {
            /** @psalm-var string */
            return $this->data['start'];
        }

        $start = null;
        /** @var string $prefix*/
        $prefix   = $this->getXpathPrefix();
        $nodeList = $this->xpath->query($prefix . '/@start');

        if ($nodeList->length > 0) {
            $node = $nodeList->item(0);
            assert($node instanceof DOMAttr);
            $start = $node->value;
        }

        $this->data['start'] = $start;
        return $this->data['start'];
    }

    public function getEnd(): ?string
    {
        if (array_key_exists('end', $this->data)) {
            /** @psalm-var string */
            return $this->data['end'];
        }

        $end = null;
        /** @var string $prefix*/
        $prefix   = $this->getXpathPrefix();
        $nodeList = $this->xpath->query($prefix . '/@end');

        if ($nodeList->length > 0) {
            $node = $nodeList->item(0);
            assert($node instanceof DOMAttr);
            $end = $node->value;
        }

        $this->data['end'] = $end;
        return $this->data['end'];
    }

    /**
     * Register PodcastIndex namespace
     */
    protected function registerNamespaces(): void
    {
        $this->getXpath()->registerNamespace(
            'podcast',
            'https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/1.0.md'
        );
    }
}
