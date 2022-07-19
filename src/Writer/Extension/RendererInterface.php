<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension;

use DOMDocument;
use DOMElement;

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
