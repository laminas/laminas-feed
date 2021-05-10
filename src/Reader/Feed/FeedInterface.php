<?php

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
     * Get a link to the HTML source
     *
     * @return null|string
     */
    public function getLink();

    /**
     * Get a link to the XML feed
     *
     * @return null|string
     */
    public function getFeedLink();

    /**
     * Get the feed title
     *
     * @return null|string
     */
    public function getTitle();

    /**
     * Get all categories
     *
     * @return Category
     */
    public function getCategories();
}
