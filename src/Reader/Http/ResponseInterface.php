<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Reader\Http;

interface ResponseInterface
{
    /**
     * Retrieve the response body
     *
     * @return string
     */
    public function getBody();

    /**
     * Retrieve the HTTP response status code
     *
     * @return int
     */
    public function getStatusCode();
}
