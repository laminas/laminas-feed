<?php

declare(strict_types=1);

namespace LaminasTest\Feed\PubSubHubbub\TestAsset;

use Laminas\Feed\PubSubHubbub\AbstractCallback;

final class Callback extends AbstractCallback
{
    /**
     * {@inheritDoc}
     *
     * @return false
     */
    public function handle(?array $httpData = null, $sendResponseNow = false)
    {
        return false;
    }
}
