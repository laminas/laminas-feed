<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\WellFormedWeb\Renderer;

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
            return; // RSS 2.0 only
        }
        $this->_setCommentFeedLinks($this->dom, $this->base);
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
            'xmlns:wfw',
            'http://wellformedweb.org/CommentAPI/'
        );
    }

    /**
     * Set entry comment feed links
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
            if ($link['type'] === 'rss') {
                $flink = $this->dom->createElement('wfw:commentRss');
                $text  = $dom->createTextNode((string) $link['uri']);
                $flink->appendChild($text);
                $root->appendChild($flink);
            }
        }
        $this->called = true;
    }

    // phpcs:enable PSR2.Methods.MethodDeclaration.Underscore
}
