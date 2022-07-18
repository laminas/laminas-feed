<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Entry;

use DateTime;
use Laminas\Feed\Reader\Collection\Category;

interface EntryInterface
{
    /**
     * Get the specified author
     *
     * @param  int $index
     * @return array<string, string>|null
     */
    public function getAuthor($index = 0);

    /**
     * Get an array with feed authors
     *
     * @return null|array|iterable
     */
    public function getAuthors();

    /**
     * Get the entry content
     *
     * @return string
     */
    public function getContent();

    /**
     * Get the entry creation date
     *
     * @return null|DateTime
     */
    public function getDateCreated();

    /**
     * Get the entry modification date
     *
     * @return null|DateTime
     */
    public function getDateModified();

    /**
     * Get the entry description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Get the entry enclosure
     *
     * @return null|object{url?: string, href?: string, length: int, type: string}
     */
    public function getEnclosure();

    /**
     * Get the entry ID
     *
     * @return string
     */
    public function getId();

    /**
     * Get a specific link
     *
     * @param  int $index
     * @return string
     */
    public function getLink($index = 0);

    /**
     * Get all links
     *
     * @return array
     */
    public function getLinks();

    /**
     * Get a permalink to the entry
     *
     * @return string
     */
    public function getPermalink();

    /**
     * Get the entry title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get the number of comments/replies for current entry
     *
     * @return int
     */
    public function getCommentCount();

    /**
     * Returns a URI pointing to the HTML page where comments can be made on this entry
     *
     * @return string
     */
    public function getCommentLink();

    /**
     * Returns a URI pointing to a feed of all comments for this entry
     *
     * @return string
     */
    public function getCommentFeedLink();

    /**
     * Get all categories
     *
     * @return Category
     */
    public function getCategories();
}
