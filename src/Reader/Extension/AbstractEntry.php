<?php

namespace Laminas\Feed\Reader\Extension;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Laminas\Feed\Reader;

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
     * XPath query
     *
     * @var string
     */
    protected $xpathPrefix = '';

    /**
     * Set the entry DOMElement
     *
     * Has side effect of setting the DOMDocument for the entry.
     *
     * @return $this
     */
    public function setEntryElement(DOMElement $entry)
    {
        $this->entry       = $entry;
        $this->domDocument = $entry->ownerDocument;
        return $this;
    }

    /**
     * Get the entry DOMElement
     *
     * @return DOMElement
     */
    public function getEntryElement()
    {
        return $this->entry;
    }

    /**
     * Set the entry key
     *
     * @param  string $entryKey
     * @return $this
     */
    public function setEntryKey($entryKey)
    {
        $this->entryKey = $entryKey;
        return $this;
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
     * Get the Entry's encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->getDomDocument()->encoding;
    }

    /**
     * Set the entry type
     *
     * Has side effect of setting xpath prefix
     *
     * @param  string $type
     * @return $this
     */
    public function setType($type)
    {
        if (null === $type) {
            $this->data['type'] = null;
            return $this;
        }

        $this->data['type'] = $type;
        if (
            $type === Reader\Reader::TYPE_RSS_10
            || $type === Reader\Reader::TYPE_RSS_090
        ) {
            $this->setXpathPrefix('//rss:item[' . ((int) $this->entryKey + 1) . ']');
            return $this;
        }

        if (
            $type === Reader\Reader::TYPE_ATOM_10
            || $type === Reader\Reader::TYPE_ATOM_03
        ) {
            $this->setXpathPrefix('//atom:entry[' . ((int) $this->entryKey + 1) . ']');
            return $this;
        }

        $this->setXpathPrefix('//item[' . ((int) $this->entryKey + 1) . ']');
        return $this;
    }

    /**
     * Get the entry type
     *
     * @return string
     */
    public function getType()
    {
        $type = $this->data['type'];
        if ($type === null) {
            $type = Reader\Reader::detectType($this->getEntryElement(), true);
            $this->setType($type);
        }

        return $type;
    }

    /**
     * Set the XPath query
     *
     * @return $this
     */
    public function setXpath(DOMXPath $xpath)
    {
        $this->xpath = $xpath;
        $this->registerNamespaces();
        return $this;
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
     * Serialize the entry to an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Get the XPath prefix
     *
     * @return string
     */
    public function getXpathPrefix()
    {
        return $this->xpathPrefix;
    }

    /**
     * Set the XPath prefix
     *
     * @param  string $prefix
     * @return $this
     */
    public function setXpathPrefix($prefix)
    {
        $this->xpathPrefix = $prefix;
        return $this;
    }

    /**
     * Register XML namespaces
     *
     * @return void
     */
    abstract protected function registerNamespaces();
}
