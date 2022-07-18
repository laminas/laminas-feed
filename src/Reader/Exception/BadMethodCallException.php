<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Exception;

use Laminas\Feed\Exception;

class BadMethodCallException extends Exception\BadMethodCallException implements ExceptionInterface
{
}
