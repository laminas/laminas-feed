<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Exception;

use Laminas\Feed\Exception;

/**
 * Feed exceptions
 *
 * Class to represent exceptions that occur during Feed operations.
 */
class InvalidArgumentException extends Exception\InvalidArgumentException implements ExceptionInterface
{
}
