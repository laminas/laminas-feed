<?php

declare(strict_types=1);

namespace LaminasTest\Feed\PubSubHubbub;

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Request;
use Laminas\Http\Response;

class ClientNotReset extends HttpClient
{
    /**
     * @param bool $clearCookies
     * @param bool $clearAuth
     * @return static
     */
    public function resetParameters($clearCookies = false, $clearAuth = true)
    {
        return $this;
    }

    /** @return Response */
    public function send(?Request $request = null)
    {
        return $this->response;
    }
}
