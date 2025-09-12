<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension\PodcastIndex\LiveItem as LiveItemWriter;
use Laminas\Feed\Writer\Renderer\Entry;
use Laminas\Feed\Writer\Writer;

/**
 * Renders PodcastIndex LiveItem data in a RSS Feed
 */
class LiveItem extends Entry\Rss
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

        /** @psalm-var LiveItemWriter $liveItemWriter */
        $liveItemWriter = $this->getDataContainer();
        $attributes = [
            'status' => $liveItemWriter->getStatus(),
            'start'  => $liveItemWriter->getStart(),
            'end'    => $liveItemWriter->getEnd(),
        ];

        $liveItem = ElementGenerator::createPodcastIndexElement($this->dom, $attributes, 'liveItem');
        $this->dom->appendChild($liveItem);

        // TODO: just loop through all existing entry methods?

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

    protected function setAttributes(DOMDocument $dom, DOMElement $root): void
    {

        /** @psalm-var null|TranscriptArray $transcript */
        $transcript = $container->getPodcastIndexTranscript();
        if ($transcript === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $transcript, 'transcript');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set live item content link
     */
    protected function setContentLink(DOMDocument $dom, DOMElement $root): void
    {
        // TODO
        return;
    }

    /**
     * Load extensions from Laminas\Feed\Writer\Entry
     *
     * @return void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _loadExtensions()
    {
        Writer::registerCoreExtensions();
        $manager = Writer::getExtensionManager();
        $all     = Writer::getExtensions();
        $exts    = $all['entryRenderer'];
        foreach ($exts as $extension) {
            $plugin = $manager->get($extension);
            $plugin->setDataContainer($this->getDataContainer());
            $plugin->setEncoding($this->getEncoding());
            $this->extensions[$extension] = $plugin;
        }
    }
}
