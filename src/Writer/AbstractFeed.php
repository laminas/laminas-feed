<?php

namespace Laminas\Feed\Writer;

use DateTime;
use DateTimeInterface;
use Laminas\Feed\Uri;
use Laminas\Validator;

class AbstractFeed
{
    /**
     * Contains all Feed level date to append in feed output
     *
     * @var array
     */
    protected $data = [];

    /**
     * Holds the value "atom" or "rss" depending on the feed type set when
     * when last exported.
     *
     * @var string
     */
    protected $type;

    /**
     * @var Extension\RendererInterface[]
     */
    protected $extensions;

    /**
     * Constructor: Primarily triggers the registration of core extensions and
     * loads those appropriate to this data container.
     */
    public function __construct()
    {
        Writer::registerCoreExtensions();
        $this->_loadExtensions();
    }

    /**
     * Set a single author
     *
     * The following option keys are supported:
     * 'name'  => (string) The name
     * 'email' => (string) An optional email
     * 'uri'   => (string) An optional and valid URI
     *
     * @return $this
     * @throws Exception\InvalidArgumentException If any value of $author not follow the format.
     */
    public function addAuthor(array $author)
    {
        // Check array values
        if (! array_key_exists('name', $author)
            || empty($author['name'])
            || ! is_string($author['name'])
        ) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: author array must include a "name" key with a non-empty string value'
            );
        }

        if (isset($author['email'])) {
            if (empty($author['email']) || ! is_string($author['email'])) {
                throw new Exception\InvalidArgumentException(
                    'Invalid parameter: "email" array value must be a non-empty string'
                );
            }
        }
        if (isset($author['uri'])) {
            if (empty($author['uri'])
                || ! is_string($author['uri'])
                || ! Uri::factory($author['uri'])->isValid()
            ) {
                throw new Exception\InvalidArgumentException(
                    'Invalid parameter: "uri" array value must be a non-empty string and valid URI/IRI'
                );
            }
        }

        $this->data['authors'][] = $author;

        return $this;
    }

    /**
     * Set an array with feed authors
     *
     * @see addAuthor
     * @return $this
     */
    public function addAuthors(array $authors)
    {
        foreach ($authors as $author) {
            $this->addAuthor($author);
        }

        return $this;
    }

    /**
     * Set the copyright entry
     *
     * @param  string $copyright
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setCopyright($copyright)
    {
        if (empty($copyright) || ! is_string($copyright)) {
            throw new Exception\InvalidArgumentException('Invalid parameter: parameter must be a non-empty string');
        }
        $this->data['copyright'] = $copyright;

        return $this;
    }

    /**
     * Set the feed creation date
     *
     * @param null|int|DateTimeInterface
     * @param DateTime|\DateTimeImmutable|int|null|string $date
     *
     * @return self
     *
     * @throws Exception\InvalidArgumentException
     */
    public function setDateCreated($date = null)
    {
        if ($date === null) {
            $date = new DateTime();
        }
        if (is_int($date)) {
            $date = new DateTime('@' . $date);
        }
        if (! $date instanceof DateTimeInterface) {
            throw new Exception\InvalidArgumentException(
                'Invalid DateTime object or UNIX Timestamp passed as parameter'
            );
        }
        $this->data['dateCreated'] = $date;

        return $this;
    }

    /**
     * Set the feed modification date
     *
     * @param null|int|DateTimeInterface
     * @param DateTime|\DateTimeImmutable|int|null|string $date
     *
     * @return self
     *
     * @throws Exception\InvalidArgumentException
     */
    public function setDateModified($date = null)
    {
        if ($date === null) {
            $date = new DateTime();
        }
        if (is_int($date)) {
            $date = new DateTime('@' . $date);
        }
        if (! $date instanceof DateTimeInterface) {
            throw new Exception\InvalidArgumentException(
                'Invalid DateTime object or UNIX Timestamp passed as parameter'
            );
        }
        $this->data['dateModified'] = $date;

        return $this;
    }

    /**
     * Set the feed last-build date. Ignored for Atom 1.0.
     *
     * @param null|int|DateTimeInterface
     * @param DateTime|\DateTimeImmutable|int|null|string $date
     *
     * @return self
     *
     * @throws Exception\InvalidArgumentException
     */
    public function setLastBuildDate($date = null)
    {
        if ($date === null) {
            $date = new DateTime();
        }
        if (is_int($date)) {
            $date = new DateTime('@' . $date);
        }
        if (! $date instanceof DateTimeInterface) {
            throw new Exception\InvalidArgumentException(
                'Invalid DateTime object or UNIX Timestamp passed as parameter'
            );
        }
        $this->data['lastBuildDate'] = $date;

        return $this;
    }

    /**
     * Set the feed description
     *
     * @param  string $description
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setDescription($description)
    {
        if (empty($description) || ! is_string($description)) {
            throw new Exception\InvalidArgumentException('Invalid parameter: parameter must be a non-empty string');
        }
        $this->data['description'] = $description;

        return $this;
    }

    /**
     * Set the feed generator entry
     *
     * @param  array|string $name
     * @param  null|string $version
     * @param  null|string $uri
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setGenerator($name, $version = null, $uri = null)
    {
        if (is_array($name)) {
            $data = $name;
            if (empty($data['name']) || ! is_string($data['name'])) {
                throw new Exception\InvalidArgumentException('Invalid parameter: "name" must be a non-empty string');
            }
            $generator = ['name' => $data['name']];
            if (isset($data['version'])) {
                if (empty($data['version']) || ! is_string($data['version'])) {
                    throw new Exception\InvalidArgumentException(
                        'Invalid parameter: "version" must be a non-empty string'
                    );
                }
                $generator['version'] = $data['version'];
            }
            if (isset($data['uri'])) {
                if (empty($data['uri']) || ! is_string($data['uri']) || ! Uri::factory($data['uri'])->isValid()) {
                    throw new Exception\InvalidArgumentException(
                        'Invalid parameter: "uri" must be a non-empty string and a valid URI/IRI'
                    );
                }
                $generator['uri'] = $data['uri'];
            }
        } else {
            if (empty($name) || ! is_string($name)) {
                throw new Exception\InvalidArgumentException('Invalid parameter: "name" must be a non-empty string');
            }
            $generator = ['name' => $name];
            if (isset($version)) {
                if (empty($version) || ! is_string($version)) {
                    throw new Exception\InvalidArgumentException(
                        'Invalid parameter: "version" must be a non-empty string'
                    );
                }
                $generator['version'] = $version;
            }
            if (isset($uri)) {
                if (empty($uri) || ! is_string($uri) || ! Uri::factory($uri)->isValid()) {
                    throw new Exception\InvalidArgumentException(
                        'Invalid parameter: "uri" must be a non-empty string and a valid URI/IRI'
                    );
                }
                $generator['uri'] = $uri;
            }
        }
        $this->data['generator'] = $generator;

        return $this;
    }

    /**
     * Set the feed ID - URI or URN (via PCRE pattern) supported
     *
     * @param  string $id
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setId($id)
    {
        // @codingStandardsIgnoreStart
        if ((empty($id) || ! is_string($id) || ! Uri::factory($id)->isValid())
            && ! preg_match("#^urn:[a-zA-Z0-9][a-zA-Z0-9\-]{1,31}:([a-zA-Z0-9\(\)\+\,\.\:\=\@\;\$\_\!\*\-]|%[0-9a-fA-F]{2})*#", $id)
            && ! $this->_validateTagUri($id)
        ) {
            // @codingStandardsIgnoreEnd
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be a non-empty string and valid URI/IRI'
            );
        }
        $this->data['id'] = $id;

        return $this;
    }

    /**
     * Validate a URI using the tag scheme (RFC 4151)
     *
     * @param  string $id
     * @return bool
     */
    // @codingStandardsIgnoreStart
    protected function _validateTagUri($id)
    {
        // @codingStandardsIgnoreEnd
        if (preg_match(
            '/^tag:(?P<name>.*),(?P<date>\d{4}-?\d{0,2}-?\d{0,2}):(?P<specific>.*)(.*:)*$/',
            $id,
            $matches
        )) {
            $dvalid = false;
            $date   = $matches['date'];
            $d6     = strtotime($date);
            if ((strlen($date) === 4) && $date <= date('Y')) {
                $dvalid = true;
            } elseif ((strlen($date) === 7) && ($d6 < strtotime('now'))) {
                $dvalid = true;
            } elseif ((strlen($date) === 10) && ($d6 < strtotime('now'))) {
                $dvalid = true;
            }
            $validator = new Validator\EmailAddress();
            if ($validator->isValid($matches['name'])) {
                $nvalid = true;
            } else {
                $nvalid = $validator->isValid('info@' . $matches['name']);
            }
            return $dvalid && $nvalid;
        }
        return false;
    }

    /**
     * Set a feed image (URI at minimum). Parameter is a single array with the
     * required key 'uri'. When rendering as RSS, the required keys are 'uri',
     * 'title' and 'link'. RSS also specifies three optional parameters 'width',
     * 'height' and 'description'. Only 'uri' is required and used for Atom rendering.
     *
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setImage(array $data)
    {
        if (empty($data['uri']) || ! is_string($data['uri'])
            || ! Uri::factory($data['uri'])->isValid()
        ) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter \'uri\' must be a non-empty string and valid URI/IRI'
            );
        }
        $this->data['image'] = $data;

        return $this;
    }

    /**
     * Set the feed language
     *
     * @param  string $language
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setLanguage($language)
    {
        if (empty($language) || ! is_string($language)) {
            throw new Exception\InvalidArgumentException('Invalid parameter: parameter must be a non-empty string');
        }
        $this->data['language'] = $language;

        return $this;
    }

    /**
     * Set a link to the HTML source
     *
     * @param  string $link
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setLink($link)
    {
        if (empty($link) || ! is_string($link) || ! Uri::factory($link)->isValid()) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: parameter must be a non-empty string and valid URI/IRI'
            );
        }
        $this->data['link'] = $link;

        return $this;
    }

    /**
     * Set a link to an XML feed for any feed type/version
     *
     * @param  string $link
     * @param  string $type
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setFeedLink($link, $type)
    {
        if (empty($link) || ! is_string($link) || ! Uri::factory($link)->isValid()) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: "link"" must be a non-empty string and valid URI/IRI'
            );
        }
        if (! in_array(strtolower($type), ['rss', 'rdf', 'atom'])) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: "type"; You must declare the type of feed the link points to, i.e. RSS, RDF or Atom'
            );
        }
        $this->data['feedLinks'][strtolower($type)] = $link;

        return $this;
    }

    /**
     * Set the feed title
     *
     * @param  string $title
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setTitle($title)
    {
        if ((empty($title) && ! is_numeric($title)) || ! is_string($title)) {
            throw new Exception\InvalidArgumentException('Invalid parameter: parameter must be a non-empty string');
        }
        $this->data['title'] = $title;

        return $this;
    }

    /**
     * Set the feed character encoding
     *
     * @param  string $encoding
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setEncoding($encoding)
    {
        if (empty($encoding) || ! is_string($encoding)) {
            throw new Exception\InvalidArgumentException('Invalid parameter: parameter must be a non-empty string');
        }
        $this->data['encoding'] = $encoding;

        return $this;
    }

    /**
     * Set the feed's base URL
     *
     * @param  string $url
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setBaseUrl($url)
    {
        if (empty($url) || ! is_string($url) || ! Uri::factory($url)->isValid()) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: "url" array value must be a non-empty string and valid URI/IRI'
            );
        }
        $this->data['baseUrl'] = $url;

        return $this;
    }

    /**
     * Add a Pubsubhubbub hub endpoint URL
     *
     * @param  string $url
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function addHub($url)
    {
        if (empty($url) || ! is_string($url) || ! Uri::factory($url)->isValid()) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: "url" array value must be a non-empty string and valid URI/IRI'
            );
        }
        if (! isset($this->data['hubs'])) {
            $this->data['hubs'] = [];
        }
        $this->data['hubs'][] = $url;

        return $this;
    }

    /**
     * Add Pubsubhubbub hub endpoint URLs
     *
     * @return $this
     */
    public function addHubs(array $urls)
    {
        foreach ($urls as $url) {
            $this->addHub($url);
        }

        return $this;
    }

    /**
     * Add a feed category
     *
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function addCategory(array $category)
    {
        if (! isset($category['term'])) {
            throw new Exception\InvalidArgumentException(
                'Each category must be an array and contain at least a "term" element'
                . ' containing the machine readable category name'
            );
        }
        if (isset($category['scheme'])) {
            if (empty($category['scheme'])
                || ! is_string($category['scheme'])
                || ! Uri::factory($category['scheme'])->isValid()
            ) {
                throw new Exception\InvalidArgumentException(
                    'The Atom scheme or RSS domain of a category must be a valid URI'
                );
            }
        }
        if (! isset($this->data['categories'])) {
            $this->data['categories'] = [];
        }
        $this->data['categories'][] = $category;

        return $this;
    }

    /**
     * Set an array of feed categories
     *
     * @return $this
     */
    public function addCategories(array $categories)
    {
        foreach ($categories as $category) {
            $this->addCategory($category);
        }

        return $this;
    }

    /**
     * Get a single author
     *
     * @param  int $index
     * @return null|string
     */
    public function getAuthor($index = 0)
    {
        if (isset($this->data['authors'][$index])) {
            return $this->data['authors'][$index];
        }

        return null;
    }

    /**
     * Get an array with feed authors
     *
     * @return null|array
     */
    public function getAuthors()
    {
        if (! array_key_exists('authors', $this->data)) {
            return null;
        }

        return $this->data['authors'];
    }

    /**
     * Get the copyright entry
     *
     * @return null|string
     */
    public function getCopyright()
    {
        if (! array_key_exists('copyright', $this->data)) {
            return null;
        }

        return $this->data['copyright'];
    }

    /**
     * Get the feed creation date
     *
     * @return null|string
     */
    public function getDateCreated()
    {
        if (! array_key_exists('dateCreated', $this->data)) {
            return null;
        }

        return $this->data['dateCreated'];
    }

    /**
     * Get the feed modification date
     *
     * @return null|string
     */
    public function getDateModified()
    {
        if (! array_key_exists('dateModified', $this->data)) {
            return null;
        }

        return $this->data['dateModified'];
    }

    /**
     * Get the feed last-build date
     *
     * @return null|string
     */
    public function getLastBuildDate()
    {
        if (! array_key_exists('lastBuildDate', $this->data)) {
            return null;
        }

        return $this->data['lastBuildDate'];
    }

    /**
     * Get the feed description
     *
     * @return null|string
     */
    public function getDescription()
    {
        if (! array_key_exists('description', $this->data)) {
            return null;
        }

        return $this->data['description'];
    }

    /**
     * Get the feed generator entry
     *
     * @return null|string
     */
    public function getGenerator()
    {
        if (! array_key_exists('generator', $this->data)) {
            return null;
        }

        return $this->data['generator'];
    }

    /**
     * Get the feed ID
     *
     * @return null|string
     */
    public function getId()
    {
        if (! array_key_exists('id', $this->data)) {
            return null;
        }

        return $this->data['id'];
    }

    /**
     * Get the feed image URI
     *
     * @return null|array
     */
    public function getImage()
    {
        if (! array_key_exists('image', $this->data)) {
            return null;
        }

        return $this->data['image'];
    }

    /**
     * Get the feed language
     *
     * @return null|string
     */
    public function getLanguage()
    {
        if (! array_key_exists('language', $this->data)) {
            return null;
        }

        return $this->data['language'];
    }

    /**
     * Get a link to the HTML source
     *
     * @return null|string
     */
    public function getLink()
    {
        if (! array_key_exists('link', $this->data)) {
            return null;
        }

        return $this->data['link'];
    }

    /**
     * Get a link to the XML feed
     *
     * @return null|string
     */
    public function getFeedLinks()
    {
        if (! array_key_exists('feedLinks', $this->data)) {
            return null;
        }
        return $this->data['feedLinks'];
    }

    /**
     * Get the feed title
     *
     * @return null|string
     */
    public function getTitle()
    {
        if (! array_key_exists('title', $this->data)) {
            return null;
        }

        return $this->data['title'];
    }

    /**
     * Get the feed character encoding
     *
     * @return null|string
     */
    public function getEncoding()
    {
        if (! array_key_exists('encoding', $this->data)) {
            return 'UTF-8';
        }

        return $this->data['encoding'];
    }

    /**
     * Get the feed's base url
     *
     * @return null|string
     */
    public function getBaseUrl()
    {
        if (! array_key_exists('baseUrl', $this->data)) {
            return null;
        }

        return $this->data['baseUrl'];
    }

    /**
     * Get the URLs used as Pubsubhubbub hubs endpoints
     *
     * @return null|string
     */
    public function getHubs()
    {
        if (! array_key_exists('hubs', $this->data)) {
            return null;
        }

        return $this->data['hubs'];
    }

    /**
     * Get the feed categories
     *
     * @return null|string
     */
    public function getCategories()
    {
        if (! array_key_exists('categories', $this->data)) {
            return null;
        }

        return $this->data['categories'];
    }

    /**
     * Resets the instance and deletes all data
     *
     * @return void
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * Set the current feed type being exported to "rss" or "atom". This allows
     * other objects to gracefully choose whether to execute or not, depending
     * on their appropriateness for the current type, e.g. renderers.
     *
     * @param  string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Retrieve the current or last feed type exported.
     *
     * @return string Value will be "rss" or "atom"
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Unset a specific data point
     *
     * @param  string $name
     * @return $this
     */
    public function remove($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
        return $this;
    }

    /**
     * Method overloading: call given method on first extension implementing it
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws Exception\BadMethodCallException if no extensions implements the method
     */
    public function __call($method, $args)
    {
        foreach ($this->extensions as $extension) {
            try {
                $callback = [$extension, $method];
                return $callback(...$args);
            } catch (Exception\BadMethodCallException $e) {
            }
        }
        throw new Exception\BadMethodCallException(
            'Method: ' . $method . ' does not exist and could not be located on a registered Extension'
        );
    }

    /**
     * Load extensions from Laminas\Feed\Writer\Writer
     *
     * @throws Exception\RuntimeException
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _loadExtensions()
    {
        // @codingStandardsIgnoreEnd
        $all     = Writer::getExtensions();
        $manager = Writer::getExtensionManager();
        $exts    = $all['feed'];
        foreach ($exts as $ext) {
            if (! $manager->has($ext)) {
                throw new Exception\RuntimeException(
                    sprintf('Unable to load extension "%s"; could not resolve to class', $ext)
                );
            }
            $this->extensions[$ext] = $manager->get($ext);
            $this->extensions[$ext]->setEncoding($this->getEncoding());
        }
    }
}
