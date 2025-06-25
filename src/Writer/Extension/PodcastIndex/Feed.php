<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex;

use DateTimeInterface;
use Laminas\Feed\Writer;
use Laminas\Stdlib\StringUtils;
use Laminas\Stdlib\StringWrapper\StringWrapperInterface;

use function array_key_exists;
use function ctype_alpha;
use function filter_var;
use function is_bool;
use function is_string;
use function lcfirst;
use function method_exists;
use function strlen;
use function substr;
use function ucfirst;

use const FILTER_VALIDATE_URL;

/**
 * Describes PodcastIndex data of a RSS Feed
 *
 * @psalm-type UpdateFrequencyArray = array{
 *   description: string,
 *   complete?: bool,
 *   dtstart?: DateTimeInterface,
 *   rrule?: string
 *   }
 * @psalm-type PersonArray = array{
 *       name: string,
 *       role?: string,
 *       group?: string,
 *       img?: string,
 *       href?: string
 *   }
 */
class Feed
{
    /**
     * Array of Feed data for rendering by Extension's renderers
     *
     * @var array
     */
    protected $data = [];

    /**
     * Encoding of all text values
     *
     * @var string
     */
    protected $encoding = 'UTF-8';

    /**
     * The used string wrapper supporting encoding
     *
     * @var StringWrapperInterface
     */
    protected $stringWrapper;

    public function __construct()
    {
        $this->stringWrapper = StringUtils::getWrapper($this->encoding);
    }

    /**
     * Set feed encoding
     */
    public function setEncoding(string $enc): Feed
    {
        $this->stringWrapper = StringUtils::getWrapper($enc);
        $this->encoding      = $enc;
        return $this;
    }

    /**
     * Get feed encoding
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Set a locked value of "yes" or "no" with an "owner" field.
     *
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexLocked(array $value): Feed
    {
        if (! isset($value['value']) || ! isset($value['owner'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "locked" must be an array containing keys "value" and "owner"'
            );
        }
        if (
            ! is_string($value['value'])
            || ! ctype_alpha($value['value']) && strlen($value['value']) > 0
        ) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "value" of "locked" may only contain alphabetic characters'
            );
        }
        $this->data['locked'] = $value;
        return $this;
    }

    /**
     * Set feed funding
     *
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexFunding(array $value): Feed
    {
        if (! isset($value['title']) || ! isset($value['url'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "funding" must be an array containing keys "title" and "url"'
            );
        }
        $this->data['funding'] = $value;
        return $this;
    }

    /**
     * Set feed license
     *
     * @param array{identifier: string, url: string} $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexLicense(array $value): self
    {
        if (! isset($value['identifier'], $value['url'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "license" must be an array containing the keys "identifier" (node value) and "url"'
            );
        }
        if (! is_string($value['identifier'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "identifier" of "license" must be of type string.'
            );
        }
        if (! is_string($value['url']) || ! filter_var($value['url'], FILTER_VALIDATE_URL)) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "url" of "license": must be a url starting with "http://" or "https://"'
            );
        }
        $this->data['license'] = $value;
        return $this;
    }

    /**
     * Set feed location
     *
     * @param array{description: string, geo?: string, osm?: string} $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexLocation(array $value): self
    {
        if (! isset($value['description'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "location" must be an array containing at least the key "description" (node value)'
            );
        }
        if (! is_string($value['description'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "description" of "location" must be of type string.'
            );
        }
        if (isset($value['geo']) && ! is_string($value['geo'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "geo" of "location" must be of type string. example: "geo:-27.86159,153.3169"'
            );
        }
        if (isset($value['osm']) && ! is_string($value['osm'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "osm" of "location" must be of type string. example: "W43678282"'
            );
        }
        $this->data['location'] = $value;
        return $this;
    }

    /**
     * Set feed images
     *
     * @param array{srcset: string} $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexImages(array $value): self
    {
        if (! isset($value['srcset'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "images" must be an array containing the key "srcset"'
            );
        }
        if (! is_string($value['srcset'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "srcset" of "images" must be of type string containing comma-seperated urls'
            );
        }
        $this->data['images'] = $value;
        return $this;
    }

    /**
     * Set feed update frequency
     *
     * @param UpdateFrequencyArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexUpdateFrequency(array $value): self
    {
        if (! isset($value['description'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "updateFrequency" must be an array containing at least the key "description"'
            );
        }
        if (! is_string($value['description'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "description" of "updateFrequency" must be of type string'
            );
        }
        if (isset($value['complete']) && ! is_bool($value['complete'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "complete" of "updateFrequency": must be of type boolean'
            );
        }
        if (isset($value['dtstart']) && ! $value['dtstart'] instanceof DateTimeInterface) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "dtstart" of "updateFrequency" must be of type DateTimeInterface'
            );
        }
        if (isset($value['rrule']) && ! is_string($value['rrule'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "rrule" of "updateFrequency" must be of type string'
            );
        }
        $this->data['updateFrequency'] = $value;
        return $this;
    }

    /**
     * Add feed person
     *
     * @psalm-param PersonArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addPodcastIndexPerson(array $value): self
    {
        if (! isset($value['name'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "person" must be an array containing at least the key "name"'
            );
        }
        if (! is_string($value['name'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "name" of "person" must be of type string'
            );
        }
        if (isset($value['role']) && ! is_string($value['role'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "role" of "person" must be of type string'
            );
        }
        if (isset($value['group']) && ! is_string($value['group'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "group" of "person" must be of type string'
            );
        }
        if (isset($value['img']) && ! filter_var($value['img'], FILTER_VALIDATE_URL)) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "img" of "person" must be a url, starting with "http://" or "https://"'
            );
        }
        if (isset($value['href']) && ! filter_var($value['href'], FILTER_VALIDATE_URL)) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "href" of "person" must be a url, starting with "http://" or "https://"'
            );
        }
        if (! isset($this->data['people'])) {
            $this->data['people'] = [];
        }

        /** @var list<PersonArray> $this->data['people'] */
        $this->data['people'][] = $value;
        return $this;
    }

    /**
     * Set a new array of people.
     * If no argument is passed, it will just remove all existing people.
     *
     * @psalm-param list<PersonArray> $values
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexPeople(array $values = []): self
    {
        $this->data['people'] = [];

        foreach ($values as $value) {
            $this->addPodcastIndexPerson($value);
        }
        return $this;
    }

    /**
     * Overloading: proxy to internal setters
     *
     * @return mixed
     * @throws Writer\Exception\BadMethodCallException
     */
    public function __call(string $method, array $params)
    {
        $point = lcfirst(substr($method, 15));
        if (
            ! method_exists($this, 'setPodcastIndex' . ucfirst($point))
            && ! method_exists($this, 'addPodcastIndex' . ucfirst($point))
        ) {
            throw new Writer\Exception\BadMethodCallException(
                'invalid method: ' . $method
            );
        }

        if (! array_key_exists($point, $this->data) || empty($this->data[$point])) {
            return;
        }
        return $this->data[$point];
    }
}
