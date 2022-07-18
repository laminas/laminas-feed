<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Exception;

use Laminas\Feed\Exception;

class InvalidArgumentException extends Exception\InvalidArgumentException implements ExceptionInterface
{
}
