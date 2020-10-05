<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\PubSubHubbub\TestAsset;

use Laminas\Feed\PubSubHubbub\AbstractCallback;

class Callback extends AbstractCallback
{
    /**
     * {@inheritDoc}
     *
     * @return false
     */
    public function handle(array $httpData = null, $sendResponseNow = false)
    {
        return false;
    }
}
