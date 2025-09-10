<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Renderer\Entry;
use Laminas\Feed\Writer\Extension;
use Laminas\Feed\Writer\Extension\PodcastIndex\LiveItem as LiveItemWriter;
use Laminas\Feed\Writer\Renderer;

/**
 * Renders PodcastIndex LiveItem data in a RSS Feed
 */
class LiveItem extends Entry\Rss
{
    /**
     * Set to TRUE if a rendering method actually renders something. This
     * is used to prevent premature appending of a XML namespace declaration
     * until an element which requires it is actually appended.
     *
     * @var bool
     */
    protected $called = false;

    public function __construct(LiveItemWriter $container)
    {
        parent::__construct($container);
    }

    /**
     * Render entry
     */
    public function render(): void
    {
        $this->dom                     = new DOMDocument('1.0', $this->container->getEncoding());
        $this->dom->formatOutput       = true;
        $this->dom->substituteEntities = false;
        $entry                         = $this->dom->createElement('item');
        $this->dom->appendChild($entry);
    }

    /**
     * Append namespaces to entry root
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    /*protected function _appendNamespaces(): void
    {
        $this->getRootElement()->setAttribute(
            'xmlns:podcast',
            'https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/1.0.md'
        );
    }*/

    /**
     * Set live item content link
     */
    protected function setContentLink(DOMDocument $dom, DOMElement $root): void
    {
        // TODO
    }
}
