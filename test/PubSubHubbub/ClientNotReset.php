<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

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
