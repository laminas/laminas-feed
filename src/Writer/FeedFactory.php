<?php

namespace Laminas\Feed\Writer;

use Traversable;

abstract class FeedFactory
{
    /**
     * Create and return a Feed based on data provided.
     *
     * @param  array|Traversable $data
     * @return Feed
     * @throws Exception\InvalidArgumentException
     */
    public static function factory($data)
    {
        if (! is_array($data) && ! $data instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable argument; received "%s"',
                __METHOD__,
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }

        $feed = new Feed();

        foreach ($data as $key => $value) {
            // Setters
            $key    = static::convertKey($key);
            $method = 'set' . $key;
            if (method_exists($feed, $method)) {
                switch ($method) {
                    case 'setfeedlink':
                        if (! is_array($value)) {
                            // Need an array
                            break;
                        }
                        if (! array_key_exists('link', $value) || ! array_key_exists('type', $value)) {
                            // Need both keys to set this correctly
                            break;
                        }
                        $feed->setFeedLink($value['link'], $value['type']);
                        break;
                    default:
                        $feed->$method($value);
                        break;
                }
                continue;
            }

            // Entries
            if ('entries' === $key) {
                static::createEntries($value, $feed);
                continue;
            }
        }

        return $feed;
    }

    /**
     * Normalize a key
     *
     * @param  string $key
     * @return string
     */
    protected static function convertKey($key)
    {
        $key = str_replace('_', '', strtolower($key));
        return $key;
    }

    /**
     * Create and attach entries to a feed
     *
     * @param  array|Traversable $entries
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    protected static function createEntries($entries, Feed $feed)
    {
        if (! is_array($entries) && ! $entries instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s::factory expects the "entries" value to be an array or Traversable; received "%s"',
                get_called_class(),
                is_object($entries) ? get_class($entries) : gettype($entries)
            ));
        }

        foreach ($entries as $data) {
            if (! is_array($data) && ! $data instanceof Traversable && ! $data instanceof Entry) {
                throw new Exception\InvalidArgumentException(sprintf(
                    '%s expects an array, Traversable, or Laminas\Feed\Writer\Entry argument; received "%s"',
                    __METHOD__,
                    is_object($data) ? get_class($data) : gettype($data)
                ));
            }

            // Use case 1: Entry item
            if ($data instanceof Entry) {
                $feed->addEntry($data);
                continue;
            }

            // Use case 2: iterate item and populate entry
            $entry = $feed->createEntry();
            foreach ($data as $key => $value) {
                $key    = static::convertKey($key);
                $method = 'set' . $key;
                if (! method_exists($entry, $method)) {
                    continue;
                }
                $entry->$method($value);
            }
            $feed->addEntry($entry);
        }
    }
}
