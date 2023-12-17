<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Http;

use Laminas\Feed\Reader\Exception;
use Laminas\Http\Client as LaminasHttpClient;
use Laminas\Http\Headers;

use function gettype;
use function is_array;
use function is_numeric;
use function is_object;
use function is_string;
use function sprintf;

class LaminasHttpClientDecorator implements HeaderAwareClientInterface
{
    private LaminasHttpClient $client;

    public function __construct(LaminasHttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return LaminasHttpClient
     */
    public function getDecoratedClient()
    {
        return $this->client;
    }

    /**
     * {@inheritDoc}
     */
    public function get($uri, array $headers = [])
    {
        $this->client->resetParameters();
        $this->client->setMethod('GET');
        $this->client->setHeaders(new Headers());
        $this->client->setUri($uri);
        if (! empty($headers)) {
            $this->injectHeaders($headers);
        }
        $response = $this->client->send();

        $headers = $response->getHeaders()->toArray();

        return new Response(
            $response->getStatusCode(),
            $response->getBody(),
            $headers
        );
    }

    /**
     * Inject header values into the client.
     *
     * @return void
     */
    private function injectHeaders(array $headerValues)
    {
        $headers = $this->client->getRequest()->getHeaders();
        foreach ($headerValues as $name => $values) {
            if (! is_string($name) || is_numeric($name) || empty($name)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Header names provided to %s::get must be non-empty, non-numeric strings; received %s',
                    self::class,
                    $name
                ));
            }

            if (! is_array($values)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Header values provided to %s::get must be arrays of values; received %s',
                    self::class,
                    is_object($values) ? $values::class : gettype($values)
                ));
            }

            foreach ($values as $value) {
                if (! is_string($value) && ! is_numeric($value)) {
                    throw new Exception\InvalidArgumentException(sprintf(
                        'Individual header values provided to %s::get must be strings or numbers; '
                        . 'received %s for header %s',
                        self::class,
                        is_object($value) ? $value::class : gettype($value),
                        $name
                    ));
                }

                $headers->addHeaderLine($name, $value);
            }
        }
    }
}
