<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\Slash\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension;

use function is_numeric;
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
            return; // RSS 2.0 only
        }
        $this->_setCommentCount($this->dom, $this->base);
        if ($this->called) {
            $this->_appendNamespaces();
        }
    }

    // phpcs:disable PSR2.Methods.MethodDeclaration.Underscore

    /**
     * Append entry namespaces
     *
     * @return void
     */
    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute(
            'xmlns:slash',
            'http://purl.org/rss/1.0/modules/slash/'
        );
    }

    /**
     * Set entry comment count
     *
     * @return void
     */
    protected function _setCommentCount(DOMDocument $dom, DOMElement $root)
    {
        $count = $this->getDataContainer()->getCommentCount();
        if (! $count || ! is_numeric($count)) {
            $count = 0;
        }
        $tcount            = $this->dom->createElement('slash:comments');
        $tcount->nodeValue = (string) $count;
        $root->appendChild($tcount);
        $this->called = true;
    }

    // phpcs:enable PSR2.Methods.MethodDeclaration.Underscore
}
