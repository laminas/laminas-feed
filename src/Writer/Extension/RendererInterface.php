<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Writer\Extension;

use DOMDocument;
use DOMElement;

/**
*/
interface RendererInterface
{
    /**
     * Set the data container
     *
     * @param  mixed $container
     * @return void
     */
    public function setDataContainer($container);

    /**
     * Retrieve container
     *
     * @return mixed
     */
    public function getDataContainer();

    /**
     * Set DOMDocument and DOMElement on which to operate
     *
     * @param  DOMDocument $dom
     * @param  DOMElement $base
     * @return void
     */
    public function setDomDocument(DOMDocument $dom, DOMElement $base);

    /**
     * Render
     *
     * @return void
     */
    public function render();
}
