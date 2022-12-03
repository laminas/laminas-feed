<?php

declare(strict_types=1);

namespace Laminas\Feed;

use function in_array;
use function parse_url;
use function strpos;

class Uri
{
    /** @var null|string */
    protected $fragment;

    /** @var null|string */
    protected $host;

    /** @var null|string */
    protected $pass;

    /** @var null|string */
    protected $path;

    /** @var null|int */
    protected $port;

    /** @var null|string */
    protected $query;

    /** @var null|string */
    protected $scheme;

    /** @var null|string */
    protected $user;

    /** @var null|bool */
    protected $valid;

    /**
     * Valid schemes
     *
     * @var string[]
     */
    protected $validSchemes = [
        'http',
        'https',
        'file',
    ];

    /**
     * @param string $uri
     */
    public function __construct($uri)
    {
        $parsed = parse_url($uri);
        if (false === $parsed) {
            $this->valid = false;
            return;
        }

        $this->scheme   = $parsed['scheme'] ?? '';
        $this->host     = $parsed['host'] ?? '';
        $this->port     = $parsed['port'] ?? null;
        $this->user     = $parsed['user'] ?? null;
        $this->pass     = $parsed['pass'] ?? null;
        $this->path     = $parsed['path'] ?? '';
        $this->query    = $parsed['query'] ?? null;
        $this->fragment = $parsed['fragment'] ?? null;
    }

    /**
     * Create an instance
     *
     * Useful for chained validations
     *
     * @param  string $uri
     * @return static
     */
    public static function factory($uri)
    {
        return new static($uri);
    }

    /**
     * Retrieve the host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Retrieve the URI path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retrieve the scheme
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Is the URI valid?
     *
     * @return bool
     */
    public function isValid()
    {
        if (false === $this->valid) {
            return false;
        }

        if ($this->scheme && ! in_array($this->scheme, $this->validSchemes, true)) {
            return false;
        }

        if ($this->host) {
            if ($this->path && 0 !== strpos($this->path, '/')) {
                return false;
            }
            return true;
        }

        // no host, but user and/or port... what?
        if ($this->user || $this->port) {
            return false;
        }

        if ($this->path) {
            // Check path-only (no host) URI
            if (0 === strpos($this->path, '//')) {
                return false;
            }
            return true;
        }

        if (! ($this->query || $this->fragment)) {
            // No host, path, query or fragment - this is not a valid URI
            return false;
        }

        return true;
    }

    /**
     * Is the URI absolute?
     *
     * @return bool
     */
    public function isAbsolute()
    {
        return ! empty($this->scheme);
    }
}
