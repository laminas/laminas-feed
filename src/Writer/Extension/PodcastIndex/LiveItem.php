<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex;

use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Entry;
use Laminas\Stdlib\StringWrapper\StringWrapperInterface;

/**
 * Describes LiveItem data in a RSS Feed
 *
 * @psalm-import-type LiveItemArray from Validator
 */
class LiveItem extends Entry
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

    protected string $status;
    protected string $start;
    protected string $end;

    /**
     * The used string wrapper supporting encoding
     *
     * @param LiveItemArray $value
     */
    public function __construct(array $value)
    {
        parent::__construct();

        Validator::validateLiveItem($value);
        $this->status = $this->data['status'] = $value['status'];
        $this->start  = $this->data['start'] = $value['start'];
        $this->end    = $this->data['end'] = $value['end'] ?? '';
    }

    /**
     * Set the podcast index live item status
     */
    public function setStatus(string $status): void
    {
        $this->status = $this->data['status'] = $status;
    }

    /**
     * Get the podcast index live item status
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set the podcast index live item start time
     */
    public function setStart(string $start): void
    {
        $this->status = $this->data['status'] = $start;
    }

    /**
     * Get the podcast index live item start time
     */
    public function getStart(): string
    {
        return $this->start;
    }

    /**
     * Set the podcast index live item end time
     */
    public function setEnd(string $end): void
    {
        $this->status = $this->data['end'] = $end;
    }

    /**
     * Get the podcast index live item end time
     */
    public function getEnd(): string
    {
        return $this->end;
    }

    /**
     * Get the podcast index live item data
     */
    /*public function getData(): array
    {
        return $this->data;
    }*/

    /**
     * Set the podcast index live item content link
     */
    public function setPodcastIndexContentLink()
    {
        // TODO
    }

    /**
     * Get the podcast index live item content link
     */
    public function getPodcastIndexContentLink()
    {
        // TODO
    }

    /**
     * Overloading: proxy to internal setters
     *
     * @return mixed
     * @throws Writer\Exception\BadMethodCallException
     */
   /* public function __call(string $method, array $params)
    {
        $point = lcfirst(substr($method, 15));
        if (
            ! method_exists($this, 'set' . ucfirst($point))
            && ! method_exists($this, 'setPodcastIndex' . ucfirst($point))
            && ! method_exists($this, 'addPodcastIndex' . ucfirst($point))
        ) {
            throw new Writer\Exception\BadMethodCallException(
                'invalid method: ' . $method
            );
        }
        if (
            ! array_key_exists($point, $this->data)
            || empty($this->data[$point])
        ) {
            return;
        }
        return $this->data[$point];
    }*/
}
