<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex;

use Laminas\Feed\Writer\Entry;

/**
 * Describes LiveItem data in a RSS Feed
 *
 * @psalm-import-type LiveItemArray from Validator
 */
final class LiveItem extends Entry
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

        $this->status = $this->data['status'] = $value['status'];
        $this->start  = $this->data['start'] = $value['start'];
        $this->end    = $this->data['end'] = $value['end'] ?? '';
        $this->type   = 'rss';
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
}
