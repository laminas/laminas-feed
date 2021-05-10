<?php

namespace Laminas\Feed\Reader\Extension\Podcast;

use DOMText;
use Laminas\Feed\Reader\Extension;

class Feed extends Extension\AbstractFeed
{
    /**
     * Get the entry author
     *
     * @return string
     */
    public function getCastAuthor()
    {
        if (isset($this->data['author'])) {
            return $this->data['author'];
        }

        $author = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:author)');

        if (! $author) {
            $author = null;
        }

        $this->data['author'] = $author;

        return $this->data['author'];
    }

    /**
     * Get the entry block
     *
     * @return string
     */
    public function getBlock()
    {
        if (isset($this->data['block'])) {
            return $this->data['block'];
        }

        $block = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:block)');

        if (! $block) {
            $block = null;
        }

        $this->data['block'] = $block;

        return $this->data['block'];
    }

    /**
     * Get the entry category
     *
     * @return null|array
     */
    public function getItunesCategories()
    {
        if (isset($this->data['categories'])) {
            return $this->data['categories'];
        }

        $categoryList = $this->xpath->query($this->getXpathPrefix() . '/itunes:category');

        $categories = [];

        if ($categoryList->length > 0) {
            foreach ($categoryList as $node) {
                $children = null;

                if ($node->childNodes->length > 0) {
                    $children = [];

                    foreach ($node->childNodes as $childNode) {
                        if (! $childNode instanceof DOMText) {
                            $children[$childNode->getAttribute('text')] = null;
                        }
                    }
                }

                $categories[$node->getAttribute('text')] = $children;
            }
        }

        if (! $categories) {
            $categories = null;
        }

        $this->data['categories'] = $categories;

        return $this->data['categories'];
    }

    /**
     * Get the entry explicit
     *
     * @return string
     */
    public function getExplicit()
    {
        if (isset($this->data['explicit'])) {
            return $this->data['explicit'];
        }

        $explicit = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:explicit)');

        if (! $explicit) {
            $explicit = null;
        }

        $this->data['explicit'] = $explicit;

        return $this->data['explicit'];
    }

    /**
     * Get the feed/podcast image
     *
     * @return string
     */
    public function getItunesImage()
    {
        if (isset($this->data['image'])) {
            return $this->data['image'];
        }

        $image = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:image/@href)');

        if (! $image) {
            $image = null;
        }

        $this->data['image'] = $image;

        return $this->data['image'];
    }

    /**
     * Get the entry keywords
     *
     * @deprecated since 2.10.0; itunes:keywords is no longer part of the
     *     iTunes podcast RSS specification.
     * @return string
     */
    public function getKeywords()
    {
        trigger_error(
            'itunes:keywords has been deprecated in the iTunes podcast RSS specification,'
            . ' and should not be relied on.',
            \E_USER_DEPRECATED
        );

        if (isset($this->data['keywords'])) {
            return $this->data['keywords'];
        }

        $keywords = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:keywords)');

        if (! $keywords) {
            $keywords = null;
        }

        $this->data['keywords'] = $keywords;

        return $this->data['keywords'];
    }

    /**
     * Get the entry's new feed url
     *
     * @return string
     */
    public function getNewFeedUrl()
    {
        if (isset($this->data['new-feed-url'])) {
            return $this->data['new-feed-url'];
        }

        $newFeedUrl = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:new-feed-url)');

        if (! $newFeedUrl) {
            $newFeedUrl = null;
        }

        $this->data['new-feed-url'] = $newFeedUrl;

        return $this->data['new-feed-url'];
    }

    /**
     * Get the entry owner
     *
     * @return string
     */
    public function getOwner()
    {
        if (isset($this->data['owner'])) {
            return $this->data['owner'];
        }

        $owner = null;

        $email = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:owner/itunes:email)');
        $name  = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:owner/itunes:name)');

        if (! empty($email)) {
            $owner = $email . (empty($name) ? '' : ' (' . $name . ')');
        } elseif (! empty($name)) {
            $owner = $name;
        }

        if (! $owner) {
            $owner = null;
        }

        $this->data['owner'] = $owner;

        return $this->data['owner'];
    }

    /**
     * Get the entry subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        if (isset($this->data['subtitle'])) {
            return $this->data['subtitle'];
        }

        $subtitle = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:subtitle)');

        if (! $subtitle) {
            $subtitle = null;
        }

        $this->data['subtitle'] = $subtitle;

        return $this->data['subtitle'];
    }

    /**
     * Get the entry summary
     *
     * @return string
     */
    public function getSummary()
    {
        if (isset($this->data['summary'])) {
            return $this->data['summary'];
        }

        $summary = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:summary)');

        if (! $summary) {
            $summary = null;
        }

        $this->data['summary'] = $summary;

        return $this->data['summary'];
    }

    /**
     * Get the type of podcast
     *
     * @return string One of "episodic" or "serial". Defaults to "episodic"
     *     if no itunes:type tag is encountered.
     */
    public function getPodcastType()
    {
        if (isset($this->data['podcastType'])) {
            return $this->data['podcastType'];
        }

        $type = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:type)');

        if (! $type) {
            $type = 'episodic';
        }

        $this->data['podcastType'] = (string) $type;

        return $this->data['podcastType'];
    }

    /**
     * Is the podcast complete (no more episodes will post)?
     *
     * @return bool
     */
    public function isComplete()
    {
        if (isset($this->data['complete'])) {
            return $this->data['complete'];
        }

        $complete = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/itunes:complete)');

        if (! $complete) {
            $complete = false;
        }

        $this->data['complete'] = $complete === 'Yes';

        return $this->data['complete'];
    }

    /**
     * Register iTunes namespace
     *
     * @return void
     */
    protected function registerNamespaces()
    {
        $this->xpath->registerNamespace('itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');
    }
}
