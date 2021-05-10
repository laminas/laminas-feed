<?php

namespace Laminas\Feed\Reader;

use DOMDocument;
use DOMElement;
use DOMXPath;

/**
 * @deprecated This (abstract) class is deprecated. Use Laminas\Feed\Reader\Entry\AbstractEntry instead.
 */
abstract class AbstractEntry
{
    /**
     * Feed entry data
     *
     * @var array
     */
    protected $data = [];

    /**
     * DOM document object
     *
     * @var DOMDocument
     */
    protected $domDocument;

    /**
     * Entry instance
     *
     * @var DOMElement
     */
    protected $entry;

    /**
     * Pointer to the current entry
     *
     * @var int
     */
    protected $entryKey = 0;

    /**
     * XPath object
     *
     * @var DOMXPath
     */
    protected $xpath;

    /**
     * Registered extensions
     *
     * @var array
     */
    protected $extensions = [];

    /**
     * @param int $entryKey
     * @param null|string $type
     */
    public function __construct(DOMElement $entry, $entryKey, $type = null)
    {
        $this->entry       = $entry;
        $this->entryKey    = $entryKey;
        $this->domDocument = $entry->ownerDocument;
        if ($type !== null) {
            $this->data['type'] = $type;
        } else {
            $this->data['type'] = Reader::detectType($entry);
        }
        $this->_loadExtensions();
    }

    /**
     * Get the DOM
     *
     * @return DOMDocument
     */
    public function getDomDocument()
    {
        return $this->domDocument;
    }

    /**
     * Get the entry element
     *
     * @return DOMElement
     */
    public function getElement()
    {
        return $this->entry;
    }

    /**
     * Get the Entry's encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        $assumed = $this->getDomDocument()->encoding;
        if (empty($assumed)) {
            $assumed = 'UTF-8';
        }
        return $assumed;
    }

    /**
     * Get entry as xml
     *
     * @return string
     */
    public function saveXml()
    {
        $dom   = new DOMDocument('1.0', $this->getEncoding());
        $entry = $dom->importNode($this->getElement(), true);
        $dom->appendChild($entry);
        return $dom->saveXML();
    }

    /**
     * Get the entry type
     *
     * @return string
     */
    public function getType()
    {
        return $this->data['type'];
    }

    /**
     * Get the XPath query object
     *
     * @return DOMXPath
     */
    public function getXpath()
    {
        if (! $this->xpath) {
            $this->setXpath(new DOMXPath($this->getDomDocument()));
        }
        return $this->xpath;
    }

    /**
     * Set the XPath query
     *
     * @return $this
     */
    public function setXpath(DOMXPath $xpath)
    {
        $this->xpath = $xpath;
        return $this;
    }

    /**
     * Get registered extensions
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Return an Extension object with the matching name (postfixed with _Entry)
     *
     * @param  string $name
     * @return null|Extension\AbstractEntry
     */
    public function getExtension($name)
    {
        if (array_key_exists($name . '\Entry', $this->extensions)) {
            return $this->extensions[$name . '\Entry'];
        }

        return null;
    }

    /**
     * Method overloading: call given method on first extension implementing it
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws Exception\BadMethodCallException if no extensions implements the method
     */
    public function __call($method, $args)
    {
        foreach ($this->extensions as $extension) {
            if (method_exists($extension, $method)) {
                return call_user_func_array([$extension, $method], $args);
            }
        }
        throw new Exception\BadMethodCallException(
            'Method: ' . $method . ' does not exist and could not be located on a registered Extension'
        );
    }

    /**
     * Load extensions from Laminas\Feed\Reader\Reader
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _loadExtensions()
    {
        // @codingStandardsIgnoreEnd
        $all  = Reader::getExtensions();
        $feed = $all['entry'];
        foreach ($feed as $extension) {
            if (in_array($extension, $all['core'])) {
                continue;
            }
            $className                    = Reader::getPluginLoader()->getClassName($extension);
            $this->extensions[$extension] = new $className(
                $this->getElement(),
                $this->entryKey,
                $this->data['type']
            );
        }
    }
}
