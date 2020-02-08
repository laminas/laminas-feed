<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Writer\Extension\Content\Renderer;

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
        $this->_setContent($this->dom, $this->base);
        if ($this->called) {
            $this->_appendNamespaces();
        }
    }

    /**
     * Append namespaces to root element
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _appendNamespaces()
    {
        // @codingStandardsIgnoreEnd
        $this->getRootElement()->setAttribute(
            'xmlns:content',
            'http://purl.org/rss/1.0/modules/content/'
        );
    }

    /**
     * Set entry content
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setContent(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $content = $this->getDataContainer()->getContent();
        if (! $content) {
            return;
        }
        $element = $dom->createElement('content:encoded');
        $root->appendChild($element);
        $cdata = $dom->createCDATASection($content);
        $element->appendChild($cdata);
        $this->called = true;
    }
}
