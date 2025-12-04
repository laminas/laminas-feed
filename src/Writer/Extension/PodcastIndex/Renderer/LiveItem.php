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
final class LiveItem extends Entry\Rss
{
    public function __construct(LiveItemWriter $container, DOMDocument $dom, DOMElement $rootElement)
    {
        parent::__construct($container);
        $this->dom         = $dom;
        $this->rootElement = $rootElement;
    }

    /**
     * Render live item
     */
    public function render(): self
    {
        /** @psalm-var string $encoding */
        $encoding                      = $this->container->getEncoding();
        $this->dom                     = new DOMDocument('1.0', $encoding);
        $this->dom->formatOutput       = true;
        $this->dom->substituteEntities = false;

        /** @psalm-var LiveItemWriter $liveItemWriter */
        $liveItemWriter = $this->getDataContainer();
        $attributes     = [
            'status' => $liveItemWriter->getStatus(),
            'start'  => $liveItemWriter->getStart(),
            'end'    => $liveItemWriter->getEnd(),
        ];

        $liveItem = ElementGenerator::createPodcastIndexElement($this->dom, $attributes, 'liveItem');
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
        return $this;
    }

    /**
     * Load extensions from Laminas\Feed\Writer\Entry
     * Override abstract renderer method to only fetch entry extensions
     *
     * @return void
     */
    // phpcs:disable PSR2.Methods.MethodDeclaration.Underscore
    protected function _loadExtensions()
    {
        Writer::registerCoreExtensions();
        $manager = Writer::getExtensionManager();
        $all     = Writer::getExtensions();
        /** @var array<array-key,string> $exts */
        $exts = $all['entryRenderer'];
        foreach ($exts as $extension) {
            /** @var mixed $plugin */
            $plugin = $manager->get($extension);
            $plugin->setDataContainer($this->getDataContainer());
            $plugin->setEncoding($this->getEncoding());
            $this->extensions[$extension] = $plugin;
        }
    }
}
