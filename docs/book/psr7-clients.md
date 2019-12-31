# Using PSR-7 Clients

As noted in the previous section, you can [substitute your own HTTP client by implementing the ClientInterface](http-clients.md#clientinterface-and-headerawareclientinterface).
In this section, we'll demonstrate doing so in order to use a client that is
[PSR-7](http://www.php-fig.org/psr/psr-7/)-capable.

## Responses

laminas-feed provides a facility to assist with generating a
`Laminas\Feed\Reader\Response` from a PSR-7 `ResponseInterface` via
`Laminas\Feed\Reader\Http\Psr7ResponseDecorator`. As such, if you have a
PSR-7-capable client, you can pass the response to this decorator, and
immediately return it from your custom client:

```php
return new Psr7ResponseDecorator($psr7Response);
```

We'll do this with our PSR-7 client.

## Guzzle

[Guzzle](http://docs.guzzlephp.org/en/latest/) is arguably the most popular HTTP
client library for PHP, and fully supports PSR-7 since version 5. Let's install
it:

```bash
$ composer require guzzlehttp/guzzle
```

We'll use the `GuzzleHttp\Client` to make our requests to feeds.

## Creating a client

From here, we'll create our client. To do this, we'll create a class that:

- implements `Laminas\Feed\Reader\Http\ClientInterface`
- accepts a `GuzzleHttp\ClientInterface` to its constructor
- uses the Guzzle client to make the request
- returns a laminas-feed response decorating the actual PSR-7 response

The code looks like this:

```php
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Laminas\Feed\Reader\Http\ClientInterface as FeedReaderHttpClientInterface;
use Laminas\Feed\Reader\Http\Psr7ResponseDecorator;

class GuzzleClient implements FeedReaderHttpClientInterface
{
    /**
     * @var GuzzleClientInterface
     */
    private $client;

    /**
     * @param GuzzleClientInterface|null $client
     */
    public function __construct(GuzzleClientInterface $client = null)
    {
        $this->client = $client ?: new Client();
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri)
    {
        return new Psr7ResponseDecorator(
			$this->client->request('GET', $uri)
		);
    }
}
```

## Using the client

In order to use our new client, we need to tell `Laminas\Feed\Reader\Reader` about
it:

```php
Laminas\Feed\Reader\Reader::setHttpClient(new GuzzleClient());
```

From this point forward, this custom client will be used to retrieve feeds.

## References

This chapter is based on [a blog post by Stefan Gehrig](https://www.teqneers.de/2016/05/laminasfeedreader-guzzle-and-psr-7/).
