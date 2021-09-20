<?php

namespace Laminas\Feed\Writer\Extension\ITunes\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension;

use function implode;
use function is_array;

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

    // phpcs:disable PSR2.Methods.MethodDeclaration.Underscore

    /**
     * Append feed namespaces
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
     * Set feed authors
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
            $text = $dom->createTextNode($author);
            $el->appendChild($text);
            $root->appendChild($el);
        }
        $this->called = true;
    }

    /**
     * Set feed itunes block
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
        $text = $dom->createTextNode($block);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed categories
     *
     * @return void
     */
    protected function _setCategories(DOMDocument $dom, DOMElement $root)
    {
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
     * Set feed cumulative duration
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
        $text = $dom->createTextNode($duration);
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
     * Set feed's new URL
     *
     * @return void
     */
    protected function _setNewFeedUrl(DOMDocument $dom, DOMElement $root)
    {
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
     * @return void
     */
    protected function _setOwners(DOMDocument $dom, DOMElement $root)
    {
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
     * @return void
     */
    protected function _setSubtitle(DOMDocument $dom, DOMElement $root)
    {
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
     * @return void
     */
    protected function _setSummary(DOMDocument $dom, DOMElement $root)
    {
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
     * @return void
     */
    protected function _setType(DOMDocument $dom, DOMElement $root)
    {
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
     * @return void
     */
    protected function _setComplete(DOMDocument $dom, DOMElement $root)
    {
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

    // phpcs:enable PSR2.Methods.MethodDeclaration.Underscore
}
