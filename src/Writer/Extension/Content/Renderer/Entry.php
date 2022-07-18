<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\Content\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension;

use function strtolower;

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

    // phpcs:disable PSR2.Methods.MethodDeclaration.Underscore

    /**
     * Append namespaces to root element
     *
     * @return void
     */
    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute(
            'xmlns:content',
            'http://purl.org/rss/1.0/modules/content/'
        );
    }

    /**
     * Set entry content
     *
     * @return void
     */
    protected function _setContent(DOMDocument $dom, DOMElement $root)
    {
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

    // phpcs:enable PSR2.Methods.MethodDeclaration.Underscore
}
