<?php

namespace Laminas\Feed\Reader\Entry;

use DateTime;
use DOMElement;
use DOMXPath;
use Laminas\Feed\Reader;

use function array_key_exists;
use function count;
use function is_array;
use function is_string;

class Atom extends AbstractEntry implements EntryInterface
{
    /**
     * XPath query
     *
     * @var string
     */
    protected $xpathQuery = '';

    /**
     * @param int $entryKey
     * @param null|string $type
     */
    public function __construct(DOMElement $entry, $entryKey, $type = null)
    {
        parent::__construct($entry, $entryKey, $type);

        // Everyone by now should know XPath indices start from 1 not 0
        $this->xpathQuery = '//atom:entry[' . ($this->entryKey + 1) . ']';

        $manager    = Reader\Reader::getExtensionManager();
        $extensions = ['Atom\Entry', 'Thread\Entry', 'DublinCore\Entry'];

        foreach ($extensions as $name) {
            $extension = $manager->get($name);
            $extension->setEntryElement($entry);
            $extension->setEntryKey($entryKey);
            $extension->setType($type);
            $this->extensions[$name] = $extension;
        }
    }

    /**
     * @inheritDoc
     * @param int $index
     * @return null|array<string, string>
     */
    public function getAuthor($index = 0)
    {
        $authors = $this->getAuthors();

        return isset($authors[$index]) && is_array($authors[$index])
            ? $authors[$index]
            : null;
    }

    /**
     * Get an array with feed authors
     *
     * @return array
     */
    public function getAuthors()
    {
        if (array_key_exists('authors', $this->data)) {
            return $this->data['authors'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $people = $this->getExtension('Atom')->getAuthors();

        $this->data['authors'] = $people;

        return $this->data['authors'];
    }

    /**
     * Get the entry content
     *
     * @return string
     */
    public function getContent()
    {
        if (array_key_exists('content', $this->data)) {
            return $this->data['content'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $content = $this->getExtension('Atom')->getContent();

        $this->data['content'] = $content;

        return $this->data['content'];
    }

    /**
     * Get the entry creation date
     *
     * @return DateTime
     */
    public function getDateCreated()
    {
        if (array_key_exists('datecreated', $this->data)) {
            return $this->data['datecreated'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $dateCreated = $this->getExtension('Atom')->getDateCreated();

        $this->data['datecreated'] = $dateCreated;

        return $this->data['datecreated'];
    }

    /**
     * Get the entry modification date
     *
     * @return DateTime
     */
    public function getDateModified()
    {
        if (array_key_exists('datemodified', $this->data)) {
            return $this->data['datemodified'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $dateModified = $this->getExtension('Atom')->getDateModified();

        $this->data['datemodified'] = $dateModified;

        return $this->data['datemodified'];
    }

    /**
     * Get the entry description
     *
     * @return string
     */
    public function getDescription()
    {
        if (array_key_exists('description', $this->data)) {
            return $this->data['description'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $description = $this->getExtension('Atom')->getDescription();

        $this->data['description'] = $description;

        return $this->data['description'];
    }

    /**
     * Get the entry enclosure
     *
     * @return string
     */
    public function getEnclosure()
    {
        if (array_key_exists('enclosure', $this->data)) {
            return $this->data['enclosure'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $enclosure = $this->getExtension('Atom')->getEnclosure();

        $this->data['enclosure'] = $enclosure;

        return $this->data['enclosure'];
    }

    /**
     * Get the entry ID
     *
     * @return string
     */
    public function getId()
    {
        if (array_key_exists('id', $this->data)) {
            return $this->data['id'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $id = $this->getExtension('Atom')->getId();

        $this->data['id'] = $id;

        return $this->data['id'];
    }

    /**
     * Get a specific link
     *
     * @param  int $index
     * @return null|string
     */
    public function getLink($index = 0)
    {
        if (! array_key_exists('links', $this->data)) {
            $this->getLinks();
        }

        return isset($this->data['links'][$index]) && is_string($this->data['links'][$index])
            ? $this->data['links'][$index]
            : null;
    }

    /**
     * Get all links
     *
     * @return array
     */
    public function getLinks()
    {
        if (array_key_exists('links', $this->data)) {
            return $this->data['links'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $links = $this->getExtension('Atom')->getLinks();

        $this->data['links'] = $links;

        return $this->data['links'];
    }

    /**
     * Get a permalink to the entry
     *
     * @return string
     */
    public function getPermalink()
    {
        return $this->getLink(0);
    }

    /**
     * Get the entry title
     *
     * @return string
     */
    public function getTitle()
    {
        if (array_key_exists('title', $this->data)) {
            return $this->data['title'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $title = $this->getExtension('Atom')->getTitle();

        $this->data['title'] = $title;

        return $this->data['title'];
    }

    /**
     * Get the number of comments/replies for current entry
     *
     * @return int
     */
    public function getCommentCount()
    {
        if (array_key_exists('commentcount', $this->data)) {
            return $this->data['commentcount'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $commentcount = $this->getExtension('Thread')->getCommentCount();

        if (! $commentcount) {
            /** @psalm-suppress PossiblyNullReference */
            $commentcount = $this->getExtension('Atom')->getCommentCount();
        }

        $this->data['commentcount'] = $commentcount;

        return $this->data['commentcount'];
    }

    /**
     * Returns a URI pointing to the HTML page where comments can be made on this entry
     *
     * @return string
     */
    public function getCommentLink()
    {
        if (array_key_exists('commentlink', $this->data)) {
            return $this->data['commentlink'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $commentlink = $this->getExtension('Atom')->getCommentLink();

        $this->data['commentlink'] = $commentlink;

        return $this->data['commentlink'];
    }

    /**
     * Returns a URI pointing to a feed of all comments for this entry
     *
     * @return string
     */
    public function getCommentFeedLink()
    {
        if (array_key_exists('commentfeedlink', $this->data)) {
            return $this->data['commentfeedlink'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $commentfeedlink = $this->getExtension('Atom')->getCommentFeedLink();

        $this->data['commentfeedlink'] = $commentfeedlink;

        return $this->data['commentfeedlink'];
    }

    /**
     * Get category data as a Reader\Reader_Collection_Category object
     *
     * @return Reader\Collection\Category
     */
    public function getCategories()
    {
        if (array_key_exists('categories', $this->data)) {
            return $this->data['categories'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $categoryCollection = $this->getExtension('Atom')->getCategories();

        if (count($categoryCollection) === 0) {
            /** @psalm-suppress PossiblyNullReference */
            $categoryCollection = $this->getExtension('DublinCore')->getCategories();
        }

        $this->data['categories'] = $categoryCollection;

        return $this->data['categories'];
    }

    /**
     * Get source feed metadata from the entry
     *
     * @return null|Reader\Feed\Atom\Source
     */
    public function getSource()
    {
        if (array_key_exists('source', $this->data)) {
            return $this->data['source'];
        }

        /** @psalm-suppress PossiblyNullReference */
        $source = $this->getExtension('Atom')->getSource();

        $this->data['source'] = $source;

        return $this->data['source'];
    }

    /**
     * Set the XPath query (incl. on all Extensions)
     *
     * @return void
     */
    public function setXpath(DOMXPath $xpath)
    {
        parent::setXpath($xpath);
        foreach ($this->extensions as $extension) {
            $extension->setXpath($this->xpath);
        }
    }
}
