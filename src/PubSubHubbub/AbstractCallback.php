<?php

declare(strict_types=1);

namespace Laminas\Feed\PubSubHubbub;

use Laminas\Http\PhpEnvironment\Response as PhpResponse;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

use function array_key_exists;
use function file_get_contents;
use function function_exists;
use function gettype;
use function intval;
use function is_array;
use function is_resource;
use function sprintf;
use function str_replace;
use function stream_get_contents;
use function strlen;
use function strpos;
use function strtoupper;
use function substr;
use function trim;

abstract class AbstractCallback implements CallbackInterface
{
    /**
     * An instance of Laminas\Feed\Pubsubhubbub\Model\SubscriptionPersistenceInterface
     * used to background save any verification tokens associated with a subscription
     * or other.
     *
     * @var Model\SubscriptionPersistenceInterface
     */
    protected $storage;

    /**
     * An instance of a class handling Http Responses. This is implemented in
     * Laminas\Feed\Pubsubhubbub\HttpResponse which shares an unenforced interface with
     * (i.e. not inherited from) Laminas\Controller\Response\Http.
     *
     * @var HttpResponse|PhpResponse
     */
    protected $httpResponse;

    /**
     * The input stream to use when retrieving the request body. Defaults to
     * php://input, but can be set to another value in order to force usage
     * of another input method. This should primarily be used for testing
     * purposes.
     *
     * @var resource|string String indicates a filename or stream to open;
     *     resource indicates an already created stream to use.
     */
    protected $inputStream = 'php://input';

    /**
     * The number of Subscribers for which any updates are on behalf of.
     *
     * @var int
     */
    protected $subscriberCount = 1;

    /**
     * Constructor; accepts an array or Traversable object to preset
     * options for the Subscriber without calling all supported setter
     * methods in turn.
     *
     * @param null|array|Traversable $options Options array or Traversable object
     */
    public function __construct($options = null)
    {
        if ($options !== null) {
            $this->setOptions($options);
        }
    }

    /**
     * Process any injected configuration options
     *
     * @param  array|Traversable $options Options array or Traversable object
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (! is_array($options)) {
            throw new Exception\InvalidArgumentException(
                'Array or Traversable object expected, got ' . gettype($options)
            );
        }

        if (is_array($options)) {
            $this->setOptions($options);
        }

        if (array_key_exists('storage', $options)) {
            $this->setStorage($options['storage']);
        }
        return $this;
    }

    /**
     * Send the response, including all headers.
     * If you wish to handle this via Laminas\Http, use the getter methods
     * to retrieve any data needed to be set on your HTTP Response object, or
     * simply give this object the HTTP Response instance to work with for you!
     *
     * @return void
     */
    public function sendResponse()
    {
        $this->getHttpResponse()->send();
    }

    /**
     * Sets an instance of Laminas\Feed\Pubsubhubbub\Model\SubscriptionPersistence used
     * to background save any verification tokens associated with a subscription
     * or other.
     *
     * @return $this
     */
    public function setStorage(Model\SubscriptionPersistenceInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * Gets an instance of Laminas\Feed\Pubsubhubbub\Model\SubscriptionPersistence used
     * to background save any verification tokens associated with a subscription
     * or other.
     *
     * @return Model\SubscriptionPersistenceInterface
     * @throws Exception\RuntimeException
     */
    public function getStorage()
    {
        if ($this->storage === null) {
            throw new Exception\RuntimeException(
                'No storage object has been set that subclasses'
                . ' Laminas\Feed\Pubsubhubbub\Model\SubscriptionPersistence'
            );
        }
        return $this->storage;
    }

    /**
     * An instance of a class handling Http Responses. This is implemented in
     * Laminas\Feed\Pubsubhubbub\HttpResponse which shares an unenforced interface with
     * (i.e. not inherited from) Laminas\Controller\Response\Http.
     *
     * @param  HttpResponse|PhpResponse $httpResponse
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setHttpResponse($httpResponse)
    {
        if (! $httpResponse instanceof HttpResponse && ! $httpResponse instanceof PhpResponse) {
            throw new Exception\InvalidArgumentException(
                'HTTP Response object must'
                . ' implement one of Laminas\Feed\Pubsubhubbub\HttpResponse or'
                . ' Laminas\Http\PhpEnvironment\Response'
            );
        }
        $this->httpResponse = $httpResponse;
        return $this;
    }

    /**
     * An instance of a class handling Http Responses. This is implemented in
     * Laminas\Feed\Pubsubhubbub\HttpResponse which shares an unenforced interface with
     * (i.e. not inherited from) Laminas\Controller\Response\Http.
     *
     * @return HttpResponse|PhpResponse
     */
    public function getHttpResponse()
    {
        if ($this->httpResponse === null) {
            $this->httpResponse = new HttpResponse();
        }
        return $this->httpResponse;
    }

    /**
     * Sets the number of Subscribers for which any updates are on behalf of.
     * In other words, is this class serving one or more subscribers? How many?
     * Defaults to 1 if left unchanged.
     *
     * @param  int|string $count
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setSubscriberCount($count)
    {
        $count = intval($count);
        if ($count <= 0) {
            throw new Exception\InvalidArgumentException(
                'Subscriber count must be'
                . ' greater than zero'
            );
        }
        $this->subscriberCount = $count;
        return $this;
    }

    /**
     * Gets the number of Subscribers for which any updates are on behalf of.
     * In other words, is this class serving one or more subscribers? How many?
     *
     * @return int
     */
    public function getSubscriberCount()
    {
        return $this->subscriberCount;
    }

    // phpcs:disable PSR2.Methods.MethodDeclaration.Underscore

    /**
     * Attempt to detect the callback URL (specifically the path forward)
     *
     * @return string
     */
    protected function _detectCallbackUrl()
    {
        $callbackUrl = null;

        // IIS7 with URL Rewrite: make sure we get the unencoded url
        // (double slash problem).
        $iisUrlRewritten = $_SERVER['IIS_WasUrlRewritten'] ?? null;
        $unencodedUrl    = $_SERVER['UNENCODED_URL'] ?? null;
        if ('1' === $iisUrlRewritten && ! empty($unencodedUrl)) {
            return $unencodedUrl;
        }

        // HTTP proxy requests setup request URI with scheme and host [and port]
        // + the URL path, only use URL path.
        if (isset($_SERVER['REQUEST_URI'])) {
            $callbackUrl = $this->buildCallbackUrlFromRequestUri();
        }

        if (null !== $callbackUrl) {
            return $callbackUrl;
        }

        if (isset($_SERVER['ORIG_PATH_INFO'])) {
            return $this->buildCallbackUrlFromOrigPathInfo();
        }

        return '';
    }

    /**
     * Get the HTTP host
     *
     * @return string
     */
    protected function _getHttpHost()
    {
        if (! empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }

        $https  = $_SERVER['HTTPS'] ?? null;
        $scheme = $https === 'on' ? 'https' : 'http';
        $name   = $_SERVER['SERVER_NAME'] ?? '';
        $port   = isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 80;

        if (
            ($scheme === 'http' && $port === 80)
            || ($scheme === 'https' && $port === 443)
        ) {
            return $name;
        }

        return sprintf('%s:%d', $name, $port);
    }

    /**
     * Retrieve a Header value from either $_SERVER or Apache
     *
     * @param  string $header
     * @return bool|string
     */
    protected function _getHeader($header)
    {
        $temp = strtoupper(str_replace('-', '_', $header));
        if (! empty($_SERVER[$temp])) {
            return $_SERVER[$temp];
        }
        $temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        if (! empty($_SERVER[$temp])) {
            return $_SERVER[$temp];
        }
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (! empty($headers[$header])) {
                return $headers[$header];
            }
        }
        return false;
    }

    /**
     * Return the raw body of the request
     *
     * @return false|string Raw body, or false if not present
     */
    protected function _getRawBody()
    {
        $body = is_resource($this->inputStream)
            ? stream_get_contents($this->inputStream)
            : file_get_contents($this->inputStream);

        return strlen(trim($body)) > 0 ? $body : false;
    }

    // phpcs:enable PSR2.Methods.MethodDeclaration.Underscore

    /**
     * Build the callback URL from the REQUEST_URI server parameter.
     *
     * @return string
     */
    private function buildCallbackUrlFromRequestUri()
    {
        $callbackUrl = $_SERVER['REQUEST_URI'];
        $https       = $_SERVER['HTTPS'] ?? null;
        $scheme      = $https === 'on' ? 'https' : 'http';
        if ($https === 'on') {
            $scheme = 'https';
        }
        $schemeAndHttpHost = $scheme . '://' . $this->_getHttpHost();
        if (strpos($callbackUrl, $schemeAndHttpHost) === 0) {
            $callbackUrl = substr($callbackUrl, strlen($schemeAndHttpHost));
        }
        return $callbackUrl;
    }

    /**
     * Build the callback URL from the ORIG_PATH_INFO server parameter.
     *
     * @return string
     */
    private function buildCallbackUrlFromOrigPathInfo()
    {
        $callbackUrl = $_SERVER['ORIG_PATH_INFO'];
        if (! empty($_SERVER['QUERY_STRING'])) {
            $callbackUrl .= '?' . $_SERVER['QUERY_STRING'];
        }
        return $callbackUrl;
    }
}
