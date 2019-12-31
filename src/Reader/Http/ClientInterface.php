<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Reader\Http;

interface ClientInterface
{
    /**
     * Make a GET request to a given URI
     *
     * @param  string $uri
     * @return ResponseInterface
     */
    public function get($uri);
}
