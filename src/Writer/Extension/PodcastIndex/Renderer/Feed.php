<?php

namespace Laminas\Feed\Writer\Extension\PodcastIndex\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension;

/**
 * Renders PodcastIndex data of a RSS Feed
 */
class Feed extends Extension\AbstractRenderer
{
    /**
     * Set to TRUE if a rendering method actually renders something. This
     * is used to prevent premature appending of a XML namespace declaration
     * until an element which requires it is actually appended.
     *
     * @var bool
     */
    protected $called = false;

    /**
     * Render feed
     */
    public function render(): void
    {
        $this->setLocked($this->dom, $this->base);
        $this->setFunding($this->dom, $this->base);
        if ($this->called) {
            $this->_appendNamespaces();
        }
    }

    /**
     * Append feed namespaces
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _appendNamespaces(): void
    {
        $this->getRootElement()->setAttribute(
            'xmlns:podcast',
            'https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/1.0.md'
        );
    }

    /**
     * Set feed lock
     */
    protected function setLocked(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var null|array<string, string> $locked */
        $locked = $this->getDataContainer()->getPodcastIndexLocked();
        if ($locked === null) {
            return;
        }
        $el   = $dom->createElement('podcast:locked');
        $text = $dom->createTextNode($locked['value']);
        $el->appendChild($text);
        $el->setAttribute('owner', $locked['owner']);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed funding
     */
    protected function setFunding(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var null|array<string, string> $funding */
        $funding = $this->getDataContainer()->getPodcastIndexFunding();
        if ($funding === null) {
            return;
        }
        $el   = $dom->createElement('podcast:locked');
        $text = $dom->createTextNode($funding['title']);
        $el->appendChild($text);
        $el->setAttribute('url', $funding['url']);
        $root->appendChild($el);
        $this->called = true;
    }
}
