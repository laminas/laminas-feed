<?php

namespace Laminas\Feed\Writer\Exception;

use Laminas\Feed\Exception;

/**
 * Feed exceptions
 *
 * Class to represent exceptions that occur during Feed operations.
 */
class BadMethodCallException extends Exception\BadMethodCallException implements ExceptionInterface
{
}
