<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\GooglePlayPodcast\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension;

class Entry extends Extension\AbstractRenderer
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
     * Render entry
     *
     * @return void
     */
    public function render()
    {
        $this->_setBlock($this->dom, $this->base);
        $this->_setExplicit($this->dom, $this->base);
        $this->_setDescription($this->dom, $this->base);
        if ($this->called) {
            $this->_appendNamespaces();
        }
    }

    // phpcs:disable PSR2.Methods.MethodDeclaration.Underscore

    /**
     * Append namespaces to entry root
     *
     * @return void
     */
    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute(
            'xmlns:googleplay',
            'http://www.google.com/schemas/play-podcasts/1.0'
        );
    }

    /**
     * Set itunes block
     *
     * @return void
     */
    protected function _setBlock(DOMDocument $dom, DOMElement $root)
    {
        $block = $this->getDataContainer()->getPlayPodcastBlock();
        if ($block === null) {
            return;
        }
        $el   = $dom->createElement('googleplay:block');
        $text = $dom->createTextNode((string) $block);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set explicit flag
     *
     * @return void
     */
    protected function _setExplicit(DOMDocument $dom, DOMElement $root)
    {
        $explicit = $this->getDataContainer()->getPlayPodcastExplicit();
        if ($explicit === null) {
            return;
        }
        $el   = $dom->createElement('googleplay:explicit');
        $text = $dom->createTextNode((string) $explicit);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set episode description
     *
     * @return void
     */
    protected function _setDescription(DOMDocument $dom, DOMElement $root)
    {
        $description = $this->getDataContainer()->getPlayPodcastDescription();
        if (! $description) {
            return;
        }
        $el   = $dom->createElement('googleplay:description');
        $text = $dom->createTextNode((string) $description);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    // phpcs:enable PSR2.Methods.MethodDeclaration.Underscore
}
