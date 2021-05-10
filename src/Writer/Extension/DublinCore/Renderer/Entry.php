<?php

namespace Laminas\Feed\Writer\Extension\DublinCore\Renderer;

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
        if (strtolower($this->getType()) === 'atom') {
            return;
        }
        $this->_setAuthors($this->dom, $this->base);
        if ($this->called) {
            $this->_appendNamespaces();
        }
    }

    /**
     * Append namespaces to entry
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _appendNamespaces()
    {
        // @codingStandardsIgnoreEnd
        $this->getRootElement()->setAttribute(
            'xmlns:dc',
            'http://purl.org/dc/elements/1.1/'
        );
    }

    /**
     * Set entry author elements
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setAuthors(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $authors = $this->getDataContainer()->getAuthors();
        if (! $authors || empty($authors)) {
            return;
        }
        foreach ($authors as $data) {
            $author = $this->dom->createElement('dc:creator');
            if (array_key_exists('name', $data)) {
                $text = $dom->createTextNode($data['name']);
                $author->appendChild($text);
                $root->appendChild($author);
            }
        }
        $this->called = true;
    }
}
