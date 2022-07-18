<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer;

use Countable;
use Iterator;
// phpcs:ignore SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use ReturnTypeWillChange;

use function array_values;
use function count;
use function krsort;
use function strtolower;
use function time;
use function ucfirst;

use const SORT_NUMERIC;

class Feed extends AbstractFeed implements Iterator, Countable
{
    /**
     * Contains all entry objects
     *
     * @var array
     */
    protected $entries = [];

    /**
     * A pointer for the iterator to keep track of the entries array
     *
     * @var int
     */
    protected $entriesKey = 0;

    /**
     * Creates a new Laminas\Feed\Writer\Entry data container for use. This is NOT
     * added to the current feed automatically, but is necessary to create a
     * container with some initial values preset based on the current feed data.
     *
     * @return Entry
     */
    public function createEntry()
    {
        $entry = new Entry();
        if ($this->getEncoding()) {
            $entry->setEncoding($this->getEncoding());
        }
        $entry->setType($this->getType());
        return $entry;
    }

    /**
     * Appends a Laminas\Feed\Writer\Deleted object representing a new entry tombstone
     * to the feed data container's internal group of entries.
     *
     * @return void
     */
    public function addTombstone(Deleted $deleted)
    {
        $this->entries[] = $deleted;
    }

    /**
     * Creates a new Laminas\Feed\Writer\Deleted data container for use. This is NOT
     * added to the current feed automatically, but is necessary to create a
     * container with some initial values preset based on the current feed data.
     *
     * @return Deleted
     */
    public function createTombstone()
    {
        $deleted  = new Deleted();
        $encoding = $this->getEncoding();
        if (null !== $encoding) {
            $deleted->setEncoding($encoding);
        }
        $deleted->setType($this->getType());
        return $deleted;
    }

    /**
     * Appends a Laminas\Feed\Writer\Entry object representing a new entry/item
     * the feed data container's internal group of entries.
     *
     * @return $this
     */
    public function addEntry(Entry $entry)
    {
        $this->entries[] = $entry;
        return $this;
    }

    /**
     * Removes a specific indexed entry from the internal queue. Entries must be
     * added to a feed container in order to be indexed.
     *
     * @param  int $index
     * @return $this
     * @throws Exception\InvalidArgumentException
     */
    public function removeEntry($index)
    {
        if (! isset($this->entries[$index])) {
            throw new Exception\InvalidArgumentException('Undefined index: ' . $index . '. Entry does not exist.');
        }
        unset($this->entries[$index]);

        return $this;
    }

    /**
     * Retrieve a specific indexed entry from the internal queue. Entries must be
     * added to a feed container in order to be indexed.
     *
     * @param  int $index
     * @return Entry
     * @throws Exception\InvalidArgumentException
     */
    public function getEntry($index = 0)
    {
        if (isset($this->entries[$index])) {
            return $this->entries[$index];
        }
        throw new Exception\InvalidArgumentException('Undefined index: ' . $index . '. Entry does not exist.');
    }

    /**
     * Orders all indexed entries by date, thus offering date ordered readable
     * content where a parser (or Homo Sapien) ignores the generic rule that
     * XML element order is irrelevant and has no intrinsic meaning.
     *
     * Using this method will alter the original indexation.
     *
     * @return $this
     */
    public function orderByDate()
    {
        /**
         * Could do with some improvement for performance perhaps
         */
        $timestamp = time();
        $entries   = [];
        foreach ($this->entries as $entry) {
            if ($entry->getDateModified()) {
                $timestamp = (int) $entry->getDateModified()->getTimestamp();
            } elseif ($entry->getDateCreated()) {
                $timestamp = (int) $entry->getDateCreated()->getTimestamp();
            }
            $entries[$timestamp] = $entry;
        }
        krsort($entries, SORT_NUMERIC);
        $this->entries = array_values($entries);

        return $this;
    }

    /**
     * Get the number of feed entries.
     * Required by the Iterator interface.
     *
     * @return int
     */
    #[ReturnTypeWillChange]
    public function count()
    {
        return count($this->entries);
    }

    /**
     * Return the current entry
     *
     * @return Entry
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        return $this->entries[$this->key()];
    }

    /**
     * Return the current feed key
     *
     * @return mixed
     */
    #[ReturnTypeWillChange]
    public function key()
    {
        return $this->entriesKey;
    }

    /**
     * Move the feed pointer forward
     *
     * @return void
     */
    #[ReturnTypeWillChange]
    public function next()
    {
        ++$this->entriesKey;
    }

    /**
     * Reset the pointer in the feed object
     *
     * @return void
     */
    #[ReturnTypeWillChange]
    public function rewind()
    {
        $this->entriesKey = 0;
    }

    /**
     * Check to see if the iterator is still valid
     *
     * @return bool
     */
    #[ReturnTypeWillChange]
    public function valid()
    {
        return 0 <= $this->entriesKey && $this->entriesKey < $this->count();
    }

    /**
     * Attempt to build and return the feed resulting from the data set
     *
     * @param  string $type The feed type "rss" or "atom" to export as
     * @param  bool $ignoreExceptions
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function export($type, $ignoreExceptions = false)
    {
        $this->setType(strtolower($type));
        $type = ucfirst($this->getType());
        if ($type !== 'Rss' && $type !== 'Atom') {
            throw new Exception\InvalidArgumentException(
                'Invalid feed type specified: ' . $type . '. Should be one of "rss" or "atom".'
            );
        }
        $renderClass = 'Laminas\\Feed\\Writer\\Renderer\\Feed\\' . $type;
        $renderer    = new $renderClass($this);
        if ($ignoreExceptions) {
            $renderer->ignoreExceptions();
        }
        return $renderer->render()->saveXml();
    }
}
