<?php

namespace Laminas\Feed\Reader;

use DOMDocument;
use DOMXPath;
use Laminas\Cache\Storage\StorageInterface as CacheStorage;
use Laminas\Feed\Reader\Exception\InvalidHttpClientException;
use Laminas\Http as LaminasHttp;
use Laminas\Stdlib\ErrorHandler;

class Reader implements ReaderImportInterface
{
    /**
     * Namespace constants
     */
    const NAMESPACE_ATOM_03 = 'http://purl.org/atom/ns#';
    const NAMESPACE_ATOM_10 = 'http://www.w3.org/2005/Atom';
    const NAMESPACE_RDF     = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
    const NAMESPACE_RSS_090 = 'http://my.netscape.com/rdf/simple/0.9/';
    const NAMESPACE_RSS_10  = 'http://purl.org/rss/1.0/';

    /**
     * Feed type constants
     */
    const TYPE_ANY              = 'any';
    const TYPE_ATOM_03          = 'atom-03';
    const TYPE_ATOM_10          = 'atom-10';
    const TYPE_ATOM_10_ENTRY    = 'atom-10-entry';
    const TYPE_ATOM_ANY         = 'atom';
    const TYPE_RSS_090          = 'rss-090';
    const TYPE_RSS_091          = 'rss-091';
    const TYPE_RSS_091_NETSCAPE = 'rss-091n';
    const TYPE_RSS_091_USERLAND = 'rss-091u';
    const TYPE_RSS_092          = 'rss-092';
    const TYPE_RSS_093          = 'rss-093';
    const TYPE_RSS_094          = 'rss-094';
    const TYPE_RSS_10           = 'rss-10';
    const TYPE_RSS_20           = 'rss-20';
    const TYPE_RSS_ANY          = 'rss';

    /**
     * Cache instance
     *
     * @var CacheStorage
     */
    protected static $cache;

    /**
     * HTTP client object to use for retrieving feeds
     *
     * @var Http\ClientInterface
     */
    protected static $httpClient;

    /**
     * Override HTTP PUT and DELETE request methods?
     *
     * @var bool
     */
    protected static $httpMethodOverride = false;

    protected static $httpConditionalGet = false;

    protected static $extensionManager;

    protected static $extensions = [
        'feed'  => [
            'DublinCore\Feed',
            'Atom\Feed',
        ],
        'entry' => [
            'Content\Entry',
            'DublinCore\Entry',
            'Atom\Entry',
        ],
        'core'  => [
            'DublinCore\Feed',
            'Atom\Feed',
            'Content\Entry',
            'DublinCore\Entry',
            'Atom\Entry',
        ],
    ];

    /**
     * Disable the ability to load external XML entities based on libxml version
     *
     * If we are using libxml < 2.9, unsafe XML entity loading must be
     * disabled with a flag.
     *
     * If we are using libxml >= 2.9, XML entity loading is disabled by default.
     *
     * @return bool
     */
    public static function disableEntityLoader($flag = true)
    {
        if (LIBXML_VERSION < 20900) {
            return libxml_disable_entity_loader($flag);
        }
        return $flag;
    }

    /**
     * Get the Feed cache
     *
     * @return CacheStorage
     */
    public static function getCache()
    {
        return static::$cache;
    }

    /**
     * Set the feed cache
     *
     * @return void
     */
    public static function setCache(CacheStorage $cache)
    {
        static::$cache = $cache;
    }

    /**
     * Set the HTTP client instance
     *
     * Sets the HTTP client object to use for retrieving the feeds.
     *
     * @param Http\ClientInterface|LaminasHttp\Client $httpClient
     * @return void
     */
    public static function setHttpClient($httpClient)
    {
        if ($httpClient instanceof LaminasHttp\Client) {
            $httpClient = new Http\LaminasHttpClientDecorator($httpClient);
        }

        if (! $httpClient instanceof Http\ClientInterface) {
            throw new InvalidHttpClientException();
        }
        static::$httpClient = $httpClient;
    }

    /**
     * Gets the HTTP client object. If none is set, a new LaminasHttp\Client will be used.
     *
     * @return Http\ClientInterface
     */
    public static function getHttpClient()
    {
        if (! static::$httpClient) {
            static::$httpClient = new Http\LaminasHttpClientDecorator(new LaminasHttp\Client());
        }

        return static::$httpClient;
    }

    /**
     * Toggle using POST instead of PUT and DELETE HTTP methods
     *
     * Some feed implementations do not accept PUT and DELETE HTTP
     * methods, or they can't be used because of proxies or other
     * measures. This allows turning on using POST where PUT and
     * DELETE would normally be used; in addition, an
     * X-Method-Override header will be sent with a value of PUT or
     * DELETE as appropriate.
     *
     * @param  bool $override Whether to override PUT and DELETE.
     * @return void
     */
    public static function setHttpMethodOverride($override = true)
    {
        static::$httpMethodOverride = $override;
    }

    /**
     * Get the HTTP override state
     *
     * @return bool
     */
    public static function getHttpMethodOverride()
    {
        return static::$httpMethodOverride;
    }

    /**
     * Set the flag indicating whether or not to use HTTP conditional GET
     *
     * @param  bool $bool
     * @return void
     */
    public static function useHttpConditionalGet($bool = true)
    {
        static::$httpConditionalGet = $bool;
    }

    /**
     * Import a feed by providing a URI
     *
     * @param  string $uri The URI to the feed
     * @param  null|string $etag OPTIONAL Last received ETag for this resource
     * @param  null|string $lastModified OPTIONAL Last-Modified value for this resource
     * @return Feed\FeedInterface
     * @throws Exception\RuntimeException
     */
    public static function import($uri, $etag = null, $lastModified = null)
    {
        $cache   = self::getCache();
        $client  = self::getHttpClient();
        $cacheId = 'Laminas_Feed_Reader_' . md5($uri);

        if (static::$httpConditionalGet && $cache) {
            $headers = [];
            $data    = $cache->getItem($cacheId);
            if ($data && $client instanceof Http\HeaderAwareClientInterface) {
                // Only check for ETag and last modified values in the cache
                // if we have a client capable of emitting headers in the first place.
                if ($etag === null) {
                    $etag = $cache->getItem($cacheId . '_etag');
                }
                if ($lastModified === null) {
                    $lastModified = $cache->getItem($cacheId . '_lastmodified');
                }
                if ($etag) {
                    $headers['If-None-Match'] = [$etag];
                }
                if ($lastModified) {
                    $headers['If-Modified-Since'] = [$lastModified];
                }
            }
            $response = $client->get($uri, $headers);
            if ($response->getStatusCode() !== 200 && $response->getStatusCode() !== 304) {
                throw new Exception\RuntimeException(
                    'Feed failed to load, got response code ' . $response->getStatusCode()
                );
            }
            if ($response->getStatusCode() == 304) {
                $responseXml = $data;
            } else {
                $responseXml = $response->getBody();
                $cache->setItem($cacheId, $responseXml);

                if ($response instanceof Http\HeaderAwareResponseInterface) {
                    if ($response->getHeaderLine('ETag', false)) {
                        $cache->setItem($cacheId . '_etag', $response->getHeaderLine('ETag'));
                    }
                    if ($response->getHeaderLine('Last-Modified', false)) {
                        $cache->setItem($cacheId . '_lastmodified', $response->getHeaderLine('Last-Modified'));
                    }
                }
            }
            return static::importString($responseXml);
        } elseif ($cache) {
            $data = $cache->getItem($cacheId);
            if ($data) {
                return static::importString($data);
            }
            $response = $client->get($uri);
            if ((int) $response->getStatusCode() !== 200) {
                throw new Exception\RuntimeException(
                    'Feed failed to load, got response code ' . $response->getStatusCode()
                );
            }
            $responseXml = $response->getBody();
            $cache->setItem($cacheId, $responseXml);
            return static::importString($responseXml);
        } else {
            $response = $client->get($uri);
            if ((int) $response->getStatusCode() !== 200) {
                throw new Exception\RuntimeException(
                    'Feed failed to load, got response code ' . $response->getStatusCode()
                );
            }
            $reader = static::importString($response->getBody());
            $reader->setOriginalSourceUri($uri);
            return $reader;
        }
    }

    /**
     * Import a feed from a remote URI
     *
     * Performs similarly to import(), except it uses the HTTP client passed to
     * the method, and does not take into account cached data.
     *
     * Primary purpose is to make it possible to use the Reader with alternate
     * HTTP client implementations.
     *
     * @param  string $uri
     * @return Feed\FeedInterface
     * @throws Exception\RuntimeException if response is not an Http\ResponseInterface
     */
    public static function importRemoteFeed($uri, Http\ClientInterface $client)
    {
        $response = $client->get($uri);
        if (! $response instanceof Http\ResponseInterface) {
            throw new Exception\RuntimeException(sprintf(
                'Did not receive a %s\Http\ResponseInterface from the provided HTTP client; received "%s"',
                __NAMESPACE__,
                is_object($response) ? get_class($response) : gettype($response)
            ));
        }

        if ((int) $response->getStatusCode() !== 200) {
            throw new Exception\RuntimeException(
                'Feed failed to load, got response code ' . $response->getStatusCode()
            );
        }
        $reader = static::importString($response->getBody());
        $reader->setOriginalSourceUri($uri);
        return $reader;
    }

    /**
     * Import a feed from a string
     *
     * @param string $string
     * @return Feed\FeedInterface
     *
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public static function importString($string)
    {
        $trimmed = trim($string);
        if (! is_string($string) || empty($trimmed)) {
            throw new Exception\InvalidArgumentException('Only non empty strings are allowed as input');
        }

        $libxmlErrflag           = libxml_use_internal_errors(true);
        $disableEntityLoaderFlag = self::disableEntityLoader();
        $dom                     = new DOMDocument();
        $status                  = $dom->loadXML(trim($string));
        foreach ($dom->childNodes as $child) {
            if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                throw new Exception\InvalidArgumentException(
                    'Invalid XML: Detected use of illegal DOCTYPE'
                );
            }
        }
        self::disableEntityLoader($disableEntityLoaderFlag);
        libxml_use_internal_errors($libxmlErrflag);

        if (! $status) {
            // Build error message
            $error = libxml_get_last_error();
            if ($error && $error->message) {
                $error->message = trim($error->message);
                $errormsg       = "DOMDocument cannot parse XML: {$error->message}";
            } else {
                $errormsg = "DOMDocument cannot parse XML: Please check the XML document's validity";
            }
            throw new Exception\RuntimeException($errormsg);
        }

        $type = static::detectType($dom);

        static::registerCoreExtensions();

        if (0 === strpos($type, 'rss')) {
            $reader = new Feed\Rss($dom, $type);
        } elseif (8 === strpos($type, 'entry')) {
            $reader = new Entry\Atom($dom->documentElement, 0, self::TYPE_ATOM_10);
        } elseif (0 === strpos($type, 'atom')) {
            $reader = new Feed\Atom($dom, $type);
        } else {
            throw new Exception\RuntimeException(
                'The URI used does not point to a '
                . 'valid Atom, RSS or RDF feed that Laminas\Feed\Reader can parse.'
            );
        }
        return $reader;
    }

    /**
     * Imports a feed from a file located at $filename.
     *
     * @param  string $filename
     * @return Feed\FeedInterface
     * @throws Exception\RuntimeException
     */
    public static function importFile($filename)
    {
        ErrorHandler::start();
        $feed = file_get_contents($filename);
        $err  = ErrorHandler::stop();
        if ($feed === false) {
            throw new Exception\RuntimeException("File '{$filename}' could not be loaded", 0, $err);
        }
        return static::importString($feed);
    }

    /**
     * Find feed links
     *
     * @param  string $uri
     * @return FeedSet
     * @throws Exception\RuntimeException
     */
    public static function findFeedLinks($uri)
    {
        $client   = static::getHttpClient();
        $response = $client->get($uri);
        if ($response->getStatusCode() !== 200) {
            throw new Exception\RuntimeException(
                "Failed to access $uri, got response code " . $response->getStatusCode()
            );
        }
        $responseHtml            = $response->getBody();
        $libxmlErrflag           = libxml_use_internal_errors(true);
        $disableEntityLoaderFlag = self::disableEntityLoader();
        $dom                     = new DOMDocument();
        $status                  = $dom->loadHTML(trim($responseHtml));
        self::disableEntityLoader($disableEntityLoaderFlag);
        libxml_use_internal_errors($libxmlErrflag);
        if (! $status) {
            // Build error message
            $error = libxml_get_last_error();
            if ($error && $error->message) {
                $error->message = trim($error->message);
                $errormsg       = "DOMDocument cannot parse HTML: {$error->message}";
            } else {
                $errormsg = "DOMDocument cannot parse HTML: Please check the XML document's validity";
            }
            throw new Exception\RuntimeException($errormsg);
        }
        $feedSet = new FeedSet();
        $links   = $dom->getElementsByTagName('link');
        $feedSet->addLinks($links, $uri);
        return $feedSet;
    }

    /**
     * Detect the feed type of the provided feed
     *
     * @param  string|DOMDocument|Feed\AbstractFeed $feed
     * @param  bool $specOnly
     * @return string
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public static function detectType($feed, $specOnly = false)
    {
        if ($feed instanceof Feed\AbstractFeed) {
            $dom = $feed->getDomDocument();
        } elseif ($feed instanceof DOMDocument) {
            $dom = $feed;
        } elseif (is_string($feed) && ! empty($feed)) {
            ErrorHandler::start(E_NOTICE | E_WARNING);
            ini_set('track_errors', 1);
            $disableEntityLoaderFlag = self::disableEntityLoader();
            $dom                     = new DOMDocument();
            $status                  = $dom->loadXML($feed);
            foreach ($dom->childNodes as $child) {
                if ($child->nodeType === XML_DOCUMENT_TYPE_NODE) {
                    throw new Exception\InvalidArgumentException(
                        'Invalid XML: Detected use of illegal DOCTYPE'
                    );
                }
            }
            self::disableEntityLoader($disableEntityLoaderFlag);
            ini_restore('track_errors');
            ErrorHandler::stop();
            if (! $status) {
                if (! isset($phpErrormsg)) {
                    if (function_exists('xdebug_is_enabled')) {
                        $phpErrormsg = '(error message not available, when XDebug is running)';
                    } else {
                        $phpErrormsg = '(error message not available)';
                    }
                }
                throw new Exception\RuntimeException("DOMDocument cannot parse XML: $phpErrormsg");
            }
        } else {
            throw new Exception\InvalidArgumentException(
                'Invalid object/scalar provided: must'
                . ' be of type Laminas\Feed\Reader\Feed, DomDocument or string'
            );
        }
        $xpath = new DOMXPath($dom);

        if ($xpath->query('/rss')->length) {
            $type    = self::TYPE_RSS_ANY;
            $version = $xpath->evaluate('string(/rss/@version)');

            if (strlen($version) > 0) {
                switch ($version) {
                    case '2.0':
                        $type = self::TYPE_RSS_20;
                        break;

                    case '0.94':
                        $type = self::TYPE_RSS_094;
                        break;

                    case '0.93':
                        $type = self::TYPE_RSS_093;
                        break;

                    case '0.92':
                        $type = self::TYPE_RSS_092;
                        break;

                    case '0.91':
                        $type = self::TYPE_RSS_091;
                        break;
                }
            }

            return $type;
        }

        $xpath->registerNamespace('rdf', self::NAMESPACE_RDF);

        if ($xpath->query('/rdf:RDF')->length) {
            $xpath->registerNamespace('rss', self::NAMESPACE_RSS_10);

            if ($xpath->query('/rdf:RDF/rss:channel')->length
                || $xpath->query('/rdf:RDF/rss:image')->length
                || $xpath->query('/rdf:RDF/rss:item')->length
                || $xpath->query('/rdf:RDF/rss:textinput')->length
            ) {
                return self::TYPE_RSS_10;
            }

            $xpath->registerNamespace('rss', self::NAMESPACE_RSS_090);

            if ($xpath->query('/rdf:RDF/rss:channel')->length
                || $xpath->query('/rdf:RDF/rss:image')->length
                || $xpath->query('/rdf:RDF/rss:item')->length
                || $xpath->query('/rdf:RDF/rss:textinput')->length
            ) {
                return self::TYPE_RSS_090;
            }
        }

        $xpath->registerNamespace('atom', self::NAMESPACE_ATOM_10);

        if ($xpath->query('//atom:feed')->length) {
            return self::TYPE_ATOM_10;
        }

        if ($xpath->query('//atom:entry')->length) {
            if ($specOnly == true) {
                return self::TYPE_ATOM_10;
            } else {
                return self::TYPE_ATOM_10_ENTRY;
            }
        }

        $xpath->registerNamespace('atom', self::NAMESPACE_ATOM_03);

        if ($xpath->query('//atom:feed')->length) {
            return self::TYPE_ATOM_03;
        }

        return self::TYPE_ANY;
    }

    /**
     * Set plugin manager for use with Extensions
     *
     * @return void
     */
    public static function setExtensionManager(ExtensionManagerInterface $extensionManager)
    {
        static::$extensionManager = $extensionManager;
    }

    /**
     * Get plugin manager for use with Extensions
     *
     * @return ExtensionManagerInterface
     */
    public static function getExtensionManager()
    {
        if (! isset(static::$extensionManager)) {
            static::setExtensionManager(new StandaloneExtensionManager());
        }
        return static::$extensionManager;
    }

    /**
     * Register an Extension by name
     *
     * @param  string $name
     * @return void
     * @throws Exception\RuntimeException if unable to resolve Extension class
     */
    public static function registerExtension($name)
    {
        if (! static::hasExtension($name)) {
            throw new Exception\RuntimeException(sprintf(
                'Could not load extension "%s" using Plugin Loader.'
                . ' Check prefix paths are configured and extension exists.',
                $name
            ));
        }

        // Return early if already registered.
        if (static::isRegistered($name)) {
            return;
        }

        $manager = static::getExtensionManager();

        $feedName = $name . '\Feed';
        if ($manager->has($feedName)) {
            static::$extensions['feed'][] = $feedName;
        }

        $entryName = $name . '\Entry';
        if ($manager->has($entryName)) {
            static::$extensions['entry'][] = $entryName;
        }
    }

    /**
     * Is a given named Extension registered?
     *
     * @param  string $extensionName
     * @return bool
     */
    public static function isRegistered($extensionName)
    {
        $feedName  = $extensionName . '\Feed';
        $entryName = $extensionName . '\Entry';
        if (in_array($feedName, static::$extensions['feed'])
            || in_array($entryName, static::$extensions['entry'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * Get a list of extensions
     *
     * @return array
     */
    public static function getExtensions()
    {
        return static::$extensions;
    }

    /**
     * Reset class state to defaults
     *
     * @return void
     */
    public static function reset()
    {
        static::$cache              = null;
        static::$httpClient         = null;
        static::$httpMethodOverride = false;
        static::$httpConditionalGet = false;
        static::$extensionManager   = null;
        static::$extensions         = [
            'feed'  => [
                'DublinCore\Feed',
                'Atom\Feed',
            ],
            'entry' => [
                'Content\Entry',
                'DublinCore\Entry',
                'Atom\Entry',
            ],
            'core'  => [
                'DublinCore\Feed',
                'Atom\Feed',
                'Content\Entry',
                'DublinCore\Entry',
                'Atom\Entry',
            ],
        ];
    }

    /**
     * Register core (default) extensions
     *
     * @return void
     */
    protected static function registerCoreExtensions()
    {
        static::registerExtension('DublinCore');
        static::registerExtension('Content');
        static::registerExtension('Atom');
        static::registerExtension('Slash');
        static::registerExtension('WellFormedWeb');
        static::registerExtension('Thread');
        static::registerExtension('Podcast');
        static::registerExtension('Podcast');

        // Added in 2.10.0; check for it conditionally
        static::hasExtension('GooglePlayPodcast')
            ? static::registerExtension('GooglePlayPodcast')
            : trigger_error(
                sprintf(
                    'Please update your %1$s\ExtensionManagerInterface implementation to add entries for'
                    . ' %1$s\Extension\GooglePlayPodcast\Entry and %1$s\Extension\GooglePlayPodcast\Feed.',
                    __NAMESPACE__
                ),
                \E_USER_NOTICE
            );

        // Added in development; check for it conditionally
        static::hasExtension('PodcastIndex')
            ? static::registerExtension('PodcastIndex')
            : trigger_error(
                sprintf(
                    'Please update your %1$s\ExtensionManagerInterface implementation to add entries for'
                    . ' %1$s\Extension\PodcastIndex\Entry and %1$s\Extension\PodcastIndex\Feed.',
                    __NAMESPACE__
                ),
                \E_USER_NOTICE
            );
    }

    /**
     * Utility method to apply array_unique operation to a multidimensional
     * array.
     *
     * @param  array
     * @return array
     */
    public static function arrayUnique(array $array)
    {
        foreach ($array as &$value) {
            $value = serialize($value);
        }
        $array = array_unique($array);
        foreach ($array as &$value) {
            $value = unserialize($value);
        }
        return $array;
    }

    /**
     * Does the extension manager have the named extension?
     *
     * This method exists to allow us to test if an extension is present in the
     * extension manager. It may be used by registerExtension() to determine if
     * the extension has items present in the manager, or by
     * registerCoreExtension() to determine if the core extension has entries
     * in the extension manager. In the latter case, this can be useful when
     * adding new extensions in a minor release, as custom extension manager
     * implementations may not yet have an entry for the extension, which would
     * then otherwise cause registerExtension() to fail.
     *
     * @param  string $name
     * @return bool
     */
    protected static function hasExtension($name)
    {
        $feedName  = $name . '\Feed';
        $entryName = $name . '\Entry';
        $manager   = static::getExtensionManager();

        return $manager->has($feedName) || $manager->has($entryName);
    }
}
