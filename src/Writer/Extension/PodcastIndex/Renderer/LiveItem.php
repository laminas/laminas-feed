<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Renderer\Entry;
use Laminas\Feed\Writer\Extension;
use Laminas\Feed\Writer\Extension\PodcastIndex\LiveItem as LiveItemWriter;
use Laminas\Feed\Writer\Renderer;

/**
 * Renders PodcastIndex LiveItem data in a RSS Feed
 */
class LiveItem extends Entry\Rss implements Renderer\RendererInterface
{
    /**
     * Set to TRUE if a rendering method actually renders something. This
     * is used to prevent premature appending of a XML namespace declaration
     * until an element which requires it is actually appended.
     *
     * @var bool
     */
    protected $called = false;

    public function __construct(LiveItemWriter $container)
    {
        parent::__construct($container);
    }

    /**
     * Render live item
     */
    public function render()
    {
        $this->dom                     = new DOMDocument('1.0', $this->container->getEncoding());
        $this->dom->formatOutput       = true;
        $this->dom->substituteEntities = false;
        $liveItem                      = $this->dom->createElement('liveItem');
        $this->dom->appendChild($liveItem);

        $this->_setTitle($this->dom, $liveItem);
        $this->_setDescription($this->dom, $liveItem);
        $this->_setDateCreated($this->dom, $liveItem);
        $this->_setDateModified($this->dom, $liveItem);
        $this->_setLink($this->dom, $liveItem);
        $this->_setId($this->dom, $liveItem);
        $this->_setAuthors($this->dom, $liveItem);
        $this->_setEnclosure($this->dom, $liveItem);
        $this->_setCommentLink($this->dom, $liveItem);
        $this->_setCategories($this->dom, $liveItem);
        foreach ($this->extensions as $ext) {
            $ext->setType($this->getType());
            $ext->setRootElement($this->getRootElement());
            $ext->setDomDocument($this->getDomDocument(), $liveItem);
            $ext->render();
        }

       // $this->setContentLink($this->dom, $liveItem);
        return $this;
    }

    /**
     * Set live item content link
     */
    protected function setContentLink(DOMDocument $dom, DOMElement $root): void
    {
        // TODO
        return;
    }
}
