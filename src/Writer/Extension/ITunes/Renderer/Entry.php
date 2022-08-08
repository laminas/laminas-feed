<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\ITunes\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension;

use function implode;

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
        $this->_setAuthors($this->dom, $this->base);
        $this->_setBlock($this->dom, $this->base);
        $this->_setDuration($this->dom, $this->base);
        $this->_setImage($this->dom, $this->base);
        $this->_setExplicit($this->dom, $this->base);
        $this->_setKeywords($this->dom, $this->base);
        $this->_setTitle($this->dom, $this->base);
        $this->_setSubtitle($this->dom, $this->base);
        $this->_setSummary($this->dom, $this->base);
        $this->_setEpisode($this->dom, $this->base);
        $this->_setEpisodeType($this->dom, $this->base);
        $this->_setClosedCaptioned($this->dom, $this->base);
        $this->_setSeason($this->dom, $this->base);
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
            'xmlns:itunes',
            'http://www.itunes.com/dtds/podcast-1.0.dtd'
        );
    }

    /**
     * Set entry authors
     *
     * @return void
     */
    protected function _setAuthors(DOMDocument $dom, DOMElement $root)
    {
        $authors = $this->getDataContainer()->getItunesAuthors();
        if (! $authors || empty($authors)) {
            return;
        }
        foreach ($authors as $author) {
            $el   = $dom->createElement('itunes:author');
            $text = $dom->createTextNode((string) $author);
            $el->appendChild($text);
            $root->appendChild($el);
            $this->called = true;
        }
    }

    /**
     * Set itunes block
     *
     * @return void
     */
    protected function _setBlock(DOMDocument $dom, DOMElement $root)
    {
        $block = $this->getDataContainer()->getItunesBlock();
        if ($block === null) {
            return;
        }
        $el   = $dom->createElement('itunes:block');
        $text = $dom->createTextNode((string) $block);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set entry duration
     *
     * @return void
     */
    protected function _setDuration(DOMDocument $dom, DOMElement $root)
    {
        $duration = $this->getDataContainer()->getItunesDuration();
        if (! $duration) {
            return;
        }
        $el   = $dom->createElement('itunes:duration');
        $text = $dom->createTextNode((string) $duration);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed image (icon)
     *
     * @return void
     */
    protected function _setImage(DOMDocument $dom, DOMElement $root)
    {
        $image = $this->getDataContainer()->getItunesImage();
        if (! $image) {
            return;
        }
        $el = $dom->createElement('itunes:image');
        $el->setAttribute('href', $image);
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
        $explicit = $this->getDataContainer()->getItunesExplicit();
        if ($explicit === null) {
            return;
        }
        $el   = $dom->createElement('itunes:explicit');
        $text = $dom->createTextNode((string) $explicit);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set entry keywords
     *
     * @return void
     */
    protected function _setKeywords(DOMDocument $dom, DOMElement $root)
    {
        $keywords = $this->getDataContainer()->getItunesKeywords();
        if (! $keywords || empty($keywords)) {
            return;
        }
        $el   = $dom->createElement('itunes:keywords');
        $text = $dom->createTextNode(implode(',', $keywords));
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set entry title
     *
     * @return void
     */
    protected function _setTitle(DOMDocument $dom, DOMElement $root)
    {
        $title = $this->getDataContainer()->getItunesTitle();
        if (! $title) {
            return;
        }
        $el   = $dom->createElement('itunes:title');
        $text = $dom->createTextNode((string) $title);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set entry subtitle
     *
     * @return void
     */
    protected function _setSubtitle(DOMDocument $dom, DOMElement $root)
    {
        $subtitle = $this->getDataContainer()->getItunesSubtitle();
        if (! $subtitle) {
            return;
        }
        $el   = $dom->createElement('itunes:subtitle');
        $text = $dom->createTextNode((string) $subtitle);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set entry summary
     *
     * @return void
     */
    protected function _setSummary(DOMDocument $dom, DOMElement $root)
    {
        $summary = $this->getDataContainer()->getItunesSummary();
        if (! $summary) {
            return;
        }
        $el   = $dom->createElement('itunes:summary');
        $text = $dom->createTextNode((string) $summary);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set entry episode number
     *
     * @return void
     */
    protected function _setEpisode(DOMDocument $dom, DOMElement $root)
    {
        $episode = $this->getDataContainer()->getItunesEpisode();
        if (! $episode) {
            return;
        }
        $el   = $dom->createElement('itunes:episode');
        $text = $dom->createTextNode((string) $episode);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set entry episode type
     *
     * @return void
     */
    protected function _setEpisodeType(DOMDocument $dom, DOMElement $root)
    {
        $type = $this->getDataContainer()->getItunesEpisodeType();
        if (! $type) {
            return;
        }
        $el   = $dom->createElement('itunes:episodeType');
        $text = $dom->createTextNode((string) $type);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set closed captioning status for episode
     *
     * @return void
     */
    protected function _setClosedCaptioned(DOMDocument $dom, DOMElement $root)
    {
        $status = $this->getDataContainer()->getItunesIsClosedCaptioned();
        if (! $status) {
            return;
        }
        $el   = $dom->createElement('itunes:isClosedCaptioned');
        $text = $dom->createTextNode('Yes');
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set entry season number
     *
     * @return void
     */
    protected function _setSeason(DOMDocument $dom, DOMElement $root)
    {
        $season = $this->getDataContainer()->getItunesSeason();
        if (! $season) {
            return;
        }
        $el   = $dom->createElement('itunes:season');
        $text = $dom->createTextNode((string) $season);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    // phpcs:enable PSR2.Methods.MethodDeclaration.Underscore
}
