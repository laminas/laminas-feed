<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension;

use DOMDocument;
use DOMElement;

abstract class AbstractRenderer implements RendererInterface
{
    /** @var DOMDocument */
    protected $dom;

    /** @var mixed */
    protected $entry;

    /** @var DOMElement */
    protected $base;

    /** @var mixed */
    protected $container;

    /** @var string */
    protected $type;

    /** @var DOMElement */
    protected $rootElement;

    /**
     * Encoding of all text values
     *
     * @var string
     */
    protected $encoding = 'UTF-8';

    /**
     * Set the data container
     *
     * @param  mixed $container
     * @return $this
     */
    public function setDataContainer($container)
    {
        $this->container = $container;
        return $this;
    }

    /**
     * Set feed encoding
     *
     * @param  string $enc
     * @return $this
     */
    public function setEncoding($enc)
    {
        $this->encoding = $enc;
        return $this;
    }

    /**
     * Get feed encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set DOMDocument and DOMElement on which to operate
     *
     * @return $this
     */
    public function setDomDocument(DOMDocument $dom, DOMElement $base)
    {
        $this->dom  = $dom;
        $this->base = $base;
        return $this;
    }

    /**
     * Get data container being rendered
     *
     * @return mixed
     */
    public function getDataContainer()
    {
        return $this->container;
    }

    /**
     * Set feed type
     *
     * @param  string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get feedtype
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set root element of document
     *
     * @return $this
     */
    public function setRootElement(DOMElement $root)
    {
        $this->rootElement = $root;
        return $this;
    }

    /**
     * Get root element
     *
     * @return DOMElement
     */
    public function getRootElement()
    {
        return $this->rootElement;
    }

    /**
     * Append namespaces to feed
     *
     * @return void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    abstract protected function _appendNamespaces();
}
