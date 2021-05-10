<?php

namespace Laminas\Feed\Writer\Extension\ITunes;

use Laminas\Feed\Uri;
use Laminas\Feed\Writer;
use Laminas\Stdlib\StringUtils;
use Laminas\Stdlib\StringWrapper\StringWrapperInterface;

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
     *
     * @param  string $enc
     * @return $this
     */
    public function setEncoding($enc)
    {
        $this->stringWrapper = StringUtils::getWrapper($enc);
        $this->encoding      = $enc;
        return $this;
    }

    /**
     * Get feed encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set a block value of "yes" or "no". You may also set an empty string.
     *
     * @param string
     *
     * @return self
     *
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesBlock($value)
    {
        if (! ctype_alpha($value) && strlen($value) > 0) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "block" may only contain alphabetic characters'
            );
        }
        if ($this->stringWrapper->strlen($value) > 255) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "block" may only contain a maximum of 255 characters'
            );
        }
        $this->data['block'] = $value;
        return $this;
    }

    /**
     * Add feed authors
     *
     * @return $this
     */
    public function addItunesAuthors(array $values)
    {
        foreach ($values as $value) {
            $this->addItunesAuthor($value);
        }
        return $this;
    }

    /**
     * Add feed author
     *
     * @param  string $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addItunesAuthor($value)
    {
        if ($this->stringWrapper->strlen($value) > 255) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: any "author" may only contain a maximum of 255 characters each'
            );
        }
        if (! isset($this->data['authors'])) {
            $this->data['authors'] = [];
        }
        $this->data['authors'][] = $value;
        return $this;
    }

    /**
     * Set feed categories
     *
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesCategories(array $values)
    {
        if (! isset($this->data['categories'])) {
            $this->data['categories'] = [];
        }
        foreach ($values as $key => $value) {
            if (! is_array($value)) {
                if ($this->stringWrapper->strlen($value) > 255) {
                    throw new Writer\Exception\InvalidArgumentException(
                        'invalid parameter: any "category" may only contain a maximum of 255 characters each'
                    );
                }
                $this->data['categories'][] = $value;
            } else {
                if ($this->stringWrapper->strlen($key) > 255) {
                    throw new Writer\Exception\InvalidArgumentException(
                        'invalid parameter: any "category" may only contain a maximum of 255 characters each'
                    );
                }
                $this->data['categories'][$key] = [];
                foreach ($value as $val) {
                    if ($this->stringWrapper->strlen($val) > 255) {
                        throw new Writer\Exception\InvalidArgumentException(
                            'invalid parameter: any "category" may only contain a maximum of 255 characters each'
                        );
                    }
                    $this->data['categories'][$key][] = $val;
                }
            }
        }
        return $this;
    }

    /**
     * Set feed image (icon)
     *
     * @param  string $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesImage($value)
    {
        if (! is_string($value) || ! Uri::factory($value)->isValid()) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "image" may only be a valid URI/IRI'
            );
        }
        if (! in_array(substr($value, -3), ['jpg', 'png'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "image" may only'
                . ' use file extension "jpg" or "png" which must be the last three'
                . ' characters of the URI (i.e. no query string or fragment)'
            );
        }
        $this->data['image'] = $value;
        return $this;
    }

    /**
     * Set feed cumulative duration
     *
     * @param  string $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesDuration($value)
    {
        $value = (string) $value;
        if (! ctype_digit($value)
            && ! preg_match('/^\d+:[0-5]{1}[0-9]{1}$/', $value)
            && ! preg_match('/^\d+:[0-5]{1}[0-9]{1}:[0-5]{1}[0-9]{1}$/', $value)
        ) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "duration" may only be of a specified [[HH:]MM:]SS format'
            );
        }
        $this->data['duration'] = $value;
        return $this;
    }

    /**
     * Set "explicit" flag
     *
     * @see https://help.apple.com/itc/podcasts_connect/#/itcb54353390
     *
     * @param  bool $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesExplicit($value)
    {
        // "yes", "no" and "clean" are valid values for a previous version
        if (! is_bool($value) && ! in_array($value, ['yes', 'no', 'clean'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "explicit" must be a boolean value'
            );
        }

        switch ($value) {
            case 'yes':
                $value = true;
                break;

            case 'no':
            case 'clean':
                $value = false;
                break;
        }

        $this->data['explicit'] = $value ? 'true' : 'false';
        return $this;
    }

    /**
     * Set feed keywords
     *
     * @deprecated since 2.10.0; itunes:keywords is no longer part of the
     *     iTunes podcast RSS specification.
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesKeywords(array $value)
    {
        trigger_error(
            'itunes:keywords has been deprecated in the iTunes podcast RSS specification,'
            . ' and should not be relied on.',
            \E_USER_DEPRECATED
        );

        if (count($value) > 12) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "keywords" may only contain a maximum of 12 terms'
            );
        }
        $concat = implode(',', $value);
        if ($this->stringWrapper->strlen($concat) > 255) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "keywords" may only'
                . ' have a concatenated length of 255 chars where terms are delimited'
                . ' by a comma'
            );
        }
        $this->data['keywords'] = $value;
        return $this;
    }

    /**
     * Set new feed URL
     *
     * @param  string $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesNewFeedUrl($value)
    {
        if (! Uri::factory($value)->isValid()) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "newFeedUrl" may only be a valid URI/IRI'
            );
        }
        $this->data['newFeedUrl'] = $value;
        return $this;
    }

    /**
     * Add feed owners
     *
     * @return $this
     */
    public function addItunesOwners(array $values)
    {
        foreach ($values as $value) {
            $this->addItunesOwner($value);
        }
        return $this;
    }

    /**
     * Add feed owner
     *
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addItunesOwner(array $value)
    {
        if (! isset($value['name']) || ! isset($value['email'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: any "owner" must be an array containing keys "name" and "email"'
            );
        }
        if ($this->stringWrapper->strlen($value['name']) > 255
            || $this->stringWrapper->strlen($value['email']) > 255
        ) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: any "owner" may only contain a maximum of 255 characters'
                . ' each for "name" and "email"'
            );
        }
        if (! isset($this->data['owners'])) {
            $this->data['owners'] = [];
        }
        $this->data['owners'][] = $value;
        return $this;
    }

    /**
     * Set feed subtitle
     *
     * @param  string $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesSubtitle($value)
    {
        if ($this->stringWrapper->strlen($value) > 255) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "subtitle" may only contain a maximum of 255 characters'
            );
        }
        $this->data['subtitle'] = $value;
        return $this;
    }

    /**
     * Set feed summary
     *
     * @param  string $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesSummary($value)
    {
        if ($this->stringWrapper->strlen($value) > 4000) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "summary" may only contain a maximum of 4000 characters'
            );
        }
        $this->data['summary'] = $value;
        return $this;
    }

    /**
     * Set podcast type
     *
     * @param  string $type
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesType($type)
    {
        $validTypes = ['episodic', 'serial'];
        if (! in_array($type, $validTypes, true)) {
            throw new Writer\Exception\InvalidArgumentException(sprintf(
                'invalid parameter: "type" MUST be one of [%s]; received %s',
                implode(', ', $validTypes),
                is_object($type) ? get_class($type) : var_export($type, true)
            ));
        }
        $this->data['type'] = $type;
        return $this;
    }

    /**
     * Set "completion" status (whether more episodes will be released)
     *
     * @param  bool $status
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setItunesComplete($status)
    {
        if (! is_bool($status)) {
            throw new Writer\Exception\InvalidArgumentException(sprintf(
                'invalid parameter: "complete" MUST be boolean; received %s',
                is_object($status) ? get_class($status) : var_export($status, true)
            ));
        }

        if (! $status) {
            return $this;
        }

        $this->data['complete'] = 'Yes';
        return $this;
    }

    /**
     * Overloading: proxy to internal setters
     *
     * @param  string $method
     * @return mixed
     * @throws Writer\Exception\BadMethodCallException
     */
    public function __call($method, array $params)
    {
        $point = lcfirst(substr($method, 9));
        if (! method_exists($this, 'setItunes' . ucfirst($point))
            && ! method_exists($this, 'addItunes' . ucfirst($point))
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
