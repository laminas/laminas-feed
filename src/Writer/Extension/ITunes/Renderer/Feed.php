<?php

namespace Laminas\Feed\Writer\Extension\ITunes\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension;

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
     *
     * @return void
     */
    public function render()
    {
        $this->_setAuthors($this->dom, $this->base);
        $this->_setBlock($this->dom, $this->base);
        $this->_setCategories($this->dom, $this->base);
        $this->_setImage($this->dom, $this->base);
        $this->_setDuration($this->dom, $this->base);
        $this->_setExplicit($this->dom, $this->base);
        $this->_setKeywords($this->dom, $this->base);
        $this->_setNewFeedUrl($this->dom, $this->base);
        $this->_setOwners($this->dom, $this->base);
        $this->_setSubtitle($this->dom, $this->base);
        $this->_setSummary($this->dom, $this->base);
        $this->_setType($this->dom, $this->base);
        $this->_setComplete($this->dom, $this->base);
        if ($this->called) {
            $this->_appendNamespaces();
        }
    }

    /**
     * Append feed namespaces
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _appendNamespaces()
    {
        // @codingStandardsIgnoreEnd
        $this->getRootElement()->setAttribute(
            'xmlns:itunes',
            'http://www.itunes.com/dtds/podcast-1.0.dtd'
        );
    }

    /**
     * Set feed authors
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setAuthors(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $authors = $this->getDataContainer()->getItunesAuthors();
        if (! $authors || empty($authors)) {
            return;
        }
        foreach ($authors as $author) {
            $el   = $dom->createElement('itunes:author');
            $text = $dom->createTextNode($author);
            $el->appendChild($text);
            $root->appendChild($el);
        }
        $this->called = true;
    }

    /**
     * Set feed itunes block
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setBlock(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $block = $this->getDataContainer()->getItunesBlock();
        if ($block === null) {
            return;
        }
        $el   = $dom->createElement('itunes:block');
        $text = $dom->createTextNode($block);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed categories
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setCategories(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $cats = $this->getDataContainer()->getItunesCategories();
        if (! $cats || empty($cats)) {
            return;
        }
        foreach ($cats as $key => $cat) {
            if (! is_array($cat)) {
                $el = $dom->createElement('itunes:category');
                $el->setAttribute('text', $cat);
                $root->appendChild($el);
            } else {
                $el = $dom->createElement('itunes:category');
                $el->setAttribute('text', $key);
                $root->appendChild($el);
                foreach ($cat as $subcat) {
                    $el2 = $dom->createElement('itunes:category');
                    $el2->setAttribute('text', $subcat);
                    $el->appendChild($el2);
                }
            }
        }
        $this->called = true;
    }

    /**
     * Set feed image (icon)
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setImage(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
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
     * Set feed cumulative duration
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setDuration(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $duration = $this->getDataContainer()->getItunesDuration();
        if (! $duration) {
            return;
        }
        $el   = $dom->createElement('itunes:duration');
        $text = $dom->createTextNode($duration);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set explicit flag
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setExplicit(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $explicit = $this->getDataContainer()->getItunesExplicit();
        if ($explicit === null) {
            return;
        }
        $el   = $dom->createElement('itunes:explicit');
        $text = $dom->createTextNode($explicit);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed keywords
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setKeywords(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
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
     * Set feed's new URL
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setNewFeedUrl(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $url = $this->getDataContainer()->getItunesNewFeedUrl();
        if (! $url) {
            return;
        }
        $el   = $dom->createElement('itunes:new-feed-url');
        $text = $dom->createTextNode($url);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed owners
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setOwners(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $owners = $this->getDataContainer()->getItunesOwners();
        if (! $owners || empty($owners)) {
            return;
        }
        foreach ($owners as $owner) {
            $el   = $dom->createElement('itunes:owner');
            $name = $dom->createElement('itunes:name');
            $text = $dom->createTextNode($owner['name']);
            $name->appendChild($text);
            $email = $dom->createElement('itunes:email');
            $text  = $dom->createTextNode($owner['email']);
            $email->appendChild($text);
            $root->appendChild($el);
            $el->appendChild($name);
            $el->appendChild($email);
        }
        $this->called = true;
    }

    /**
     * Set feed subtitle
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setSubtitle(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $subtitle = $this->getDataContainer()->getItunesSubtitle();
        if (! $subtitle) {
            return;
        }
        $el   = $dom->createElement('itunes:subtitle');
        $text = $dom->createTextNode($subtitle);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed summary
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setSummary(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $summary = $this->getDataContainer()->getItunesSummary();
        if (! $summary) {
            return;
        }
        $el   = $dom->createElement('itunes:summary');
        $text = $dom->createTextNode($summary);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set podcast type
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setType(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $type = $this->getDataContainer()->getItunesType();
        if (! $type) {
            return;
        }
        $el   = $dom->createElement('itunes:type');
        $text = $dom->createTextNode($type);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set complete status
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setComplete(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $status = $this->getDataContainer()->getItunesComplete();
        if (! $status) {
            return;
        }
        $el   = $dom->createElement('itunes:complete');
        $text = $dom->createTextNode('Yes');
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }
}
