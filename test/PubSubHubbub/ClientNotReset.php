<?php

namespace LaminasTest\Feed\PubSubHubbub;

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Request;

class ClientNotReset extends HttpClient
{
    public function resetParameters($clearCookies = false, $clearAuth = true)
    {
        // Do nothing
    }

    public function send(Request $request = null)
    {
        return $this->response;
    }
}
