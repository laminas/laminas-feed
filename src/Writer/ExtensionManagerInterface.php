<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Writer;

interface ExtensionManagerInterface
{
    /**
     * Do we have the extension?
     *
     * @param  string $extension
     * @return bool
     */
    public function has($extension);

    /**
     * Retrieve the extension
     *
     * @param  string $extension
     * @return mixed
     */
    public function get($extension);
}
