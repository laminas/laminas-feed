<?php

namespace Laminas\Feed\Writer\Renderer\Feed;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Renderer;

use function array_key_exists;

class AtomSource extends AbstractAtom implements Renderer\RendererInterface
{
    public function __construct(Writer\Source $container)
    {
        parent::__construct($container);
    }

    /**
     * Render Atom Feed Metadata (Source element)
     *
     * @return $this
     */
    public function render()
    {
        if (! $this->container->getEncoding()) {
            $this->container->setEncoding('UTF-8');
        }
        $this->dom               = new DOMDocument('1.0', $this->container->getEncoding());
        $this->dom->formatOutput = true;
        $root                    = $this->dom->createElement('source');
        $this->setRootElement($root);
        $this->dom->appendChild($root);
        $this->_setLanguage($this->dom, $root);
        $this->_setBaseUrl($this->dom, $root);
        $this->_setTitle($this->dom, $root);
        $this->_setDescription($this->dom, $root);
        $this->_setDateCreated($this->dom, $root);
        $this->_setDateModified($this->dom, $root);
        $this->_setGenerator($this->dom, $root);
        $this->_setLink($this->dom, $root);
        $this->_setFeedLinks($this->dom, $root);
        $this->_setId($this->dom, $root);
        $this->_setAuthors($this->dom, $root);
        $this->_setCopyright($this->dom, $root);
        $this->_setCategories($this->dom, $root);

        foreach ($this->extensions as $ext) {
            $ext->setType($this->getType());
            $ext->setRootElement($this->getRootElement());
            $ext->setDomDocument($this->getDomDocument(), $root);
            $ext->render();
        }
        return $this;
    }

    /**
     * Set feed generator string
     *
     * @return void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _setGenerator(DOMDocument $dom, DOMElement $root)
    {
        if (! $this->getDataContainer()->getGenerator()) {
            return;
        }

        $gdata     = $this->getDataContainer()->getGenerator();
        $generator = $dom->createElement('generator');
        $root->appendChild($generator);
        $text = $dom->createTextNode($gdata['name']);
        $generator->appendChild($text);
        if (array_key_exists('uri', $gdata)) {
            $generator->setAttribute('uri', $gdata['uri']);
        }
        if (array_key_exists('version', $gdata)) {
            $generator->setAttribute('version', $gdata['version']);
        }
    }
}
