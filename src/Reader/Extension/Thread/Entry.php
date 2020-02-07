<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Reader\Extension\Thread;

use Laminas\Feed\Reader\Extension;

class Entry extends Extension\AbstractEntry
{
    /**
     * Get the "in-reply-to" value
     *
     * @return string
     */
    public function getInReplyTo()
    {
        // TODO: to be implemented
    }

    // TODO: Implement "replies" and "updated" constructs from standard

    /**
     * Get the total number of threaded responses (i.e comments)
     *
     * @return null|int
     */
    public function getCommentCount()
    {
        return $this->getData('total');
    }

    /**
     * Get the entry data specified by name
     *
     * @param  string $name
     * @return null|mixed
     */
    protected function getData($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $data = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/thread10:' . $name . ')');

        if (! $data) {
            $data = null;
        }

        $this->data[$name] = $data;

        return $data;
    }

    /**
     * Register Atom Thread Extension 1.0 namespace
     *
     * @return void
     */
    protected function registerNamespaces()
    {
        $this->xpath->registerNamespace('thread10', 'http://purl.org/syndication/thread/1.0');
    }
}
