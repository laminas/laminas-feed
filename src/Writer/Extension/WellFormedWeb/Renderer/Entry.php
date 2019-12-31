<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Writer\Extension\WellFormedWeb\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension;

/**
*/
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
        if (strtolower($this->getType()) == 'atom') {
            return; // RSS 2.0 only
        }
        $this->_setCommentFeedLinks($this->dom, $this->base);
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
            'xmlns:wfw',
            'http://wellformedweb.org/CommentAPI/'
        );
    }

    /**
     * Set entry comment feed links
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
            if ($link['type'] == 'rss') {
                $flink = $this->dom->createElement('wfw:commentRss');
                $text = $dom->createTextNode($link['uri']);
                $flink->appendChild($text);
                $root->appendChild($flink);
            }
        }
        $this->called = true;
    }
}
