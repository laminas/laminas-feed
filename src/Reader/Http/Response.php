<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Http;

use Laminas\Feed\Reader\Exception;

use function gettype;
use function intval;
use function is_numeric;
use function is_object;
use function is_string;
use function method_exists;
use function sprintf;
use function strtolower;
use function trim;

class Response implements HeaderAwareResponseInterface
{
    private string $body;

    private array $headers;

    private int $statusCode;

    /**
     * @param  int $statusCode
     * @param  object|string $body
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($statusCode, $body = '', array $headers = [])
    {
        $this->validateStatusCode($statusCode);
        $this->validateBody($body);
        $this->validateHeaders($headers);

        $this->statusCode = (int) $statusCode;
        $this->body       = (string) $body;
        $this->headers    = $this->normalizeHeaders($headers);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritDoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaderLine($name, $default = null)
    {
        $normalizedName = strtolower($name);
        return $this->headers[$normalizedName] ?? $default;
    }

    /**
     * Validate that we have a status code argument that will work for our context.
     *
     * @param int $statusCode
     * @throws Exception\InvalidArgumentException For arguments not castable
     *     to integer HTTP status codes.
     * @return void
     */
    private function validateStatusCode($statusCode)
    {
        if (! is_numeric($statusCode) || (is_string($statusCode) && trim($statusCode) !== $statusCode)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a numeric status code; received %s',
                self::class,
                is_object($statusCode) ? $statusCode::class : gettype($statusCode)
            ));
        }

        if (100 > $statusCode || 599 < $statusCode) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an integer status code between 100 and 599 inclusive; received %s',
                self::class,
                $statusCode
            ));
        }

        if (intval($statusCode) !== $statusCode) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an integer status code; received %s',
                self::class,
                $statusCode
            ));
        }
    }

    /**
     * Validate that we have a body argument that will work for our context.
     *
     * @param mixed $body
     * @throws Exception\InvalidArgumentException For arguments not castable
     *     to strings.
     * @return void
     */
    private function validateBody($body)
    {
        if (is_string($body)) {
            return;
        }

        if (is_object($body) && method_exists($body, '__toString')) {
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            '%s expects a string body, or an object that can cast to string; received %s',
            self::class,
            is_object($body) ? $body::class : gettype($body)
        ));
    }

    /**
     * Validate header values.
     *
     * @throws Exception\InvalidArgumentException
     * @return void
     */
    private function validateHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            if (! is_string($name) || is_numeric($name) || empty($name)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Header names provided to %s must be non-empty, non-numeric strings; received %s',
                    self::class,
                    $name
                ));
            }

            if (! is_string($value) && ! is_numeric($value)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Individual header values provided to %s must be a string or numeric; received %s for header %s',
                    self::class,
                    is_object($value) ? $value::class : gettype($value),
                    $name
                ));
            }
        }
    }

    /**
     * Normalize header names to lowercase.
     *
     * @return array
     */
    private function normalizeHeaders(array $headers)
    {
        $normalized = [];
        foreach ($headers as $name => $value) {
            $normalized[strtolower($name)] = $value;
        }
        return $normalized;
    }
}
