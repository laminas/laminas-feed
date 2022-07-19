<?php

declare(strict_types=1);

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
