<?php

namespace LaminasTest\Feed\Reader\TestAsset;

/**
 * This class may be used as a dummy for testing the return value from a
 * PSR-7 message's getBody() method. It does not implement the StreamInterface,
 * as PHP prior to version 7 does not do any return typehinting, making strict
 * adherence unnecessary.
 */
class Psr7Stream
{
    private $streamValue;

    public function __construct($streamValue)
    {
        $this->streamValue = $streamValue;
    }

    public function __toString()
    {
        return (string) $this->streamValue;
    }
}
