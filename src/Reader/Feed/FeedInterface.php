<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Reader\Feed;

use Countable;
use DateTime;
use Iterator;
use Laminas\Feed\Reader\Collection\Category;

interface FeedInterface extends Iterator, Countable
{
    /**
     * Get a single author
     *
     * @param  int $index
     * @return null|string
     */
    public function getAuthor($index = 0);

    /**
     * Get an array with feed authors
     *
     * @return array
     */
    public function getAuthors();

    /**
     * Get the copyright entry
     *
     * @return null|string
     */
    public function getCopyright();

    /**
     * Get the feed creation date
     *
     * @return null|DateTime
     */
    public function getDateCreated();

    /**
     * Get the feed modification date
     *
     * @return null|DateTime
     */
    public function getDateModified();

    /**
     * Get the feed lastBuild date
     *
     * @return null|DateTime
     */
    public function getLastBuildDate();

    /**
     * Get the feed description
     *
     * @return null|string
     */
    public function getDescription();

    /**
     * Get the feed generator entry
     *
     * @return null|string
     */
    public function getGenerator();

    /**
     * Get the feed ID
     *
     * @return null|string
     */
    public function getId();

    /**
     * Get the feed language
     *
     * @return null|string
     */
    public function getLanguage();

    /**
     * Get a link to the source website
     *
     * @return null|string
     */
    public function getBaseUrl();

    /**
     * Get a link to the HTML source
     *
     * @return null|string
     */
    public function getLink();

    /**
     * Get feed image data
     *
     * @return null|array
     */
    public function getImage();

    /**
     * Get a link to the XML feed
     *
     * @return null|string
     */
    public function getFeedLink();

    /**
     * Set an original source URI for the feed being parsed. This value
     * is returned from getFeedLink() method if the feed does not carry
     * a self-referencing URI.
     *
     * @param string $uri
     *
     * @return void
     */
    public function setOriginalSourceUri($uri);

    /**
     * Get the feed title
     *
     * @return null|string
     */
    public function getTitle();

    /**
     * Get an array of any supported PubSubHubbub endpoints
     *
     * @return null|array
     */
    public function getHubs();

    /**
     * Get all categories
     *
     * @return Category
     */
    public function getCategories();
}
