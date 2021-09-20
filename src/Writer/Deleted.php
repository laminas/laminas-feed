<?php

namespace Laminas\Feed\Writer;

use DateTime;
use DateTimeInterface;
use Laminas\Feed\Uri;

use function array_key_exists;
use function is_int;
use function is_string;

class Deleted
{
    /**
     * Internal array containing all data associated with this entry or item.
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
     * Set the feed character encoding
     *
     * @param  null|string $encoding
     * @return static
     * @throws Exception\InvalidArgumentException
     */
    public function setEncoding($encoding)
    {
        if (empty($encoding)) {
            throw new Exception\InvalidArgumentException('Invalid parameter: parameter must be a non-empty string');
        }
        $this->data['encoding'] = $encoding;

        return $this;
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
     * Set reference
     *
     * @param  string $reference
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setReference($reference)
    {
        if (empty($reference) || ! is_string($reference)) {
            throw new Exception\InvalidArgumentException('Invalid parameter: reference must be a non-empty string');
        }
        $this->data['reference'] = $reference;

        return $this;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        if (! array_key_exists('reference', $this->data)) {
            return;
        }
        return $this->data['reference'];
    }

    /**
     * Set when
     *
     * @param  null|int|DateTimeInterface $date
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setWhen($date = null)
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
        $this->data['when'] = $date;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getWhen()
    {
        if (! array_key_exists('when', $this->data)) {
            return;
        }
        return $this->data['when'];
    }

    /**
     * Set by
     *
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function setBy(array $by)
    {
        $author = [];
        if (
            ! array_key_exists('name', $by)
            || empty($by['name'])
            || ! is_string($by['name'])
        ) {
            throw new Exception\InvalidArgumentException(
                'Invalid parameter: author array must include a "name" key with a non-empty string value'
            );
        }
        $author['name'] = $by['name'];
        if (isset($by['email'])) {
            if (empty($by['email']) || ! is_string($by['email'])) {
                throw new Exception\InvalidArgumentException(
                    'Invalid parameter: "email" array value must be a non-empty string'
                );
            }
            $author['email'] = $by['email'];
        }
        if (isset($by['uri'])) {
            if (
                empty($by['uri'])
                || ! is_string($by['uri'])
                || ! Uri::factory($by['uri'])->isValid()
            ) {
                throw new Exception\InvalidArgumentException(
                    'Invalid parameter: "uri" array value must be a non-empty string and valid URI/IRI'
                );
            }
            $author['uri'] = $by['uri'];
        }
        $this->data['by'] = $author;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getBy()
    {
        if (! array_key_exists('by', $this->data)) {
            return null;
        }

        return $this->data['by'];
    }

    /**
     * @param  string $comment
     * @return $this
     */
    public function setComment($comment)
    {
        $this->data['comment'] = $comment;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getComment()
    {
        if (! array_key_exists('comment', $this->data)) {
            return null;
        }

        return $this->data['comment'];
    }
}
