<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Writer\Extension\Threading\Renderer;

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

    /**
     * Append entry namespaces
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _appendNamespaces()
    {
        // @codingStandardsIgnoreEnd
        $this->getRootElement()->setAttribute(
            'xmlns:thr',
            'http://purl.org/syndication/thread/1.0'
        );
    }

    /**
     * Set comment link
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setCommentLink(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
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
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setCommentFeedLinks(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
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
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _setCommentCount(DOMDocument $dom, DOMElement $root)
    {
        // @codingStandardsIgnoreEnd
        $count = $this->getDataContainer()->getCommentCount();
        if ($count === null) {
            return;
        }
        $tcount            = $this->dom->createElement('thr:total');
        $tcount->nodeValue = $count;
        $root->appendChild($tcount);
        $this->called = true;
    }
}
