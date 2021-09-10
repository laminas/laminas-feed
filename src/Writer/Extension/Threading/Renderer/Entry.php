<?php

namespace Laminas\Feed\Writer\Extension\Threading\Renderer;

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
        if (strtolower($this->getType()) === 'rss') {
            return; // Atom 1.0 only
        }
        $this->_setCommentLink($this->dom, $this->base);
        $this->_setCommentFeedLinks($this->dom, $this->base);
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
            'xmlns:thr',
            'http://purl.org/syndication/thread/1.0'
        );
    }

    /**
     * Set comment link
     *
     * @return void
     */
    protected function _setCommentLink(DOMDocument $dom, DOMElement $root)
    {
        $link = $this->getDataContainer()->getCommentLink();
        if (! $link) {
            return;
        }
        $clink = $this->dom->createElement('link');
        $clink->setAttribute('rel', 'replies');
        $clink->setAttribute('type', 'text/html');
        $clink->setAttribute('href', $link);
        $count = $this->getDataContainer()->getCommentCount();
        if ($count !== null) {
            $clink->setAttribute('thr:count', $count);
        }
        $root->appendChild($clink);
        $this->called = true;
    }

    /**
     * Set comment feed links
     *
     * @return void
     */
    protected function _setCommentFeedLinks(DOMDocument $dom, DOMElement $root)
    {
        $links = $this->getDataContainer()->getCommentFeedLinks();
        if (! $links || empty($links)) {
            return;
        }
        foreach ($links as $link) {
            $flink = $this->dom->createElement('link');
            $flink->setAttribute('rel', 'replies');
            $flink->setAttribute('type', 'application/' . $link['type'] . '+xml');
            $flink->setAttribute('href', $link['uri']);
            $count = $this->getDataContainer()->getCommentCount();
            if ($count !== null) {
                $flink->setAttribute('thr:count', $count);
            }
            $root->appendChild($flink);
            $this->called = true;
        }
    }

    /**
     * Set entry comment count
     *
     * @return void
     */
    protected function _setCommentCount(DOMDocument $dom, DOMElement $root)
    {
        $count = $this->getDataContainer()->getCommentCount();
        if ($count === null) {
            return;
        }
        $tcount            = $this->dom->createElement('thr:total');
        $tcount->nodeValue = $count;
        $root->appendChild($tcount);
        $this->called = true;
    }

    // phpcs:enable PSR2.Methods.MethodDeclaration.Underscore
}
