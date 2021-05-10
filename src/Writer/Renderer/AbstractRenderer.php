<?php

namespace Laminas\Feed\Writer\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer;

class AbstractRenderer
{
    /**
     * Extensions
     *
     * @var array
     */
    protected $extensions = [];

    /**
     * @var Writer\AbstractFeed
     */
    protected $container;

    /**
     * @var DOMDocument
     */
    protected $dom;

    /**
     * @var bool
     */
    protected $ignoreExceptions = false;

    /**
     * @var array
     */
    protected $exceptions = [];

    /**
     * Encoding of all text values
     *
     * @var string
     */
    protected $encoding = 'UTF-8';

    /**
     * Holds the value "atom" or "rss" depending on the feed type set when
     * when last exported.
     *
     * @var string
     */
    protected $type;

    /**
     * @var DOMElement
     */
    protected $rootElement;

    /**
     * @param Writer\AbstractFeed $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->setType($container->getType());
        $this->_loadExtensions();
    }

    /**
     * Save XML to string
     *
     * @return string
     */
    public function saveXml()
    {
        return $this->getDomDocument()->saveXML();
    }

    /**
     * Get DOM document
     *
     * @return DOMDocument
     */
    public function getDomDocument()
    {
        return $this->dom;
    }

    /**
     * Get document element from DOM
     *
     * @return DOMElement
     */
    public function getElement()
    {
        return $this->getDomDocument()->documentElement;
    }

    /**
     * Get data container of items being rendered
     *
     * @return Writer\AbstractFeed
     */
    public function getDataContainer()
    {
        return $this->container;
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
     * Indicate whether or not to ignore exceptions
     *
     * @param  bool $bool
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function ignoreExceptions($bool = true)
    {
        if (! is_bool($bool)) {
            throw new Writer\Exception\InvalidArgumentException(
                'Invalid parameter: $bool. Should be TRUE or FALSE (defaults to TRUE if null)'
            );
        }
        $this->ignoreExceptions = $bool;
        return $this;
    }

    /**
     * Get exception list
     *
     * @return array
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * Set the current feed type being exported to "rss" or "atom". This allows
     * other objects to gracefully choose whether to execute or not, depending
     * on their appropriateness for the current type, e.g. renderers.
     *
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Retrieve the current or last feed type exported.
     *
     * @return string Value will be "rss" or "atom"
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the absolute root element for the XML feed being generated. This
     * helps simplify the appending of namespace declarations, but also ensures
     * namespaces are added to the root element - not scattered across the entire
     * XML file - may assist namespace unsafe parsers and looks pretty ;).
     *
     * @return void
     */
    public function setRootElement(DOMElement $root)
    {
        $this->rootElement = $root;
    }

    /**
     * Retrieve the absolute root element for the XML feed being generated.
     *
     * @return DOMElement
     */
    public function getRootElement()
    {
        return $this->rootElement;
    }

    /**
     * Load extensions from Laminas\Feed\Writer\Writer
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _loadExtensions()
    {
        // @codingStandardsIgnoreEnd
        Writer\Writer::registerCoreExtensions();
        $manager = Writer\Writer::getExtensionManager();
        $all     = Writer\Writer::getExtensions();
        $exts    = stripos(get_class($this), 'entry')
            ? $all['entryRenderer']
            : $all['feedRenderer'];
        foreach ($exts as $extension) {
            $plugin = $manager->get($extension);
            $plugin->setDataContainer($this->getDataContainer());
            $plugin->setEncoding($this->getEncoding());
            $this->extensions[$extension] = $plugin;
        }
    }
}
