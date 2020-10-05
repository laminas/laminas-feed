<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\PubSubHubbub;

use Laminas\Http\Client as HttpClient;

class ClientNotReset extends HttpClient
{
    /**
     * @return void
     */
    public function resetParameters($clearCookies = false, $clearAuth = true)
    {
        // Do nothing
    }
}
