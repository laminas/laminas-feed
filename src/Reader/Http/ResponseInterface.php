<?php

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
