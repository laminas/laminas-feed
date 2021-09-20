<?php

namespace Laminas\Feed\Reader\Entry;

use DateTime;
use DOMElement;
use DOMXPath;
use Laminas\Feed\Reader;
use Laminas\Feed\Reader\Collection\Author as AuthorCollection;
use Laminas\Feed\Reader\Exception\RuntimeException;
use Laminas\Feed\Reader\Extension\Atom\Entry;
use Laminas\Feed\Reader\Extension\DublinCore\Entry as DublinCoreEntry;
use Laminas\Feed\Reader\Extension\Thread\Entry as ThreadEntry;

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
     * @return AuthorCollection
     */
    public function getAuthors()
    {
        if (array_key_exists('authors', $this->data)) {
            return $this->data['authors'];
        }

        $people = $this->getAtomExtension()->getAuthors();

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

        $content = $this->getAtomExtension()->getContent();

        $this->data['content'] = $content;

        return $this->data['content'];
    }

    /**
     * Get the entry creation date
     *
     * @return null|DateTime
     */
    public function getDateCreated()
    {
        if (array_key_exists('datecreated', $this->data)) {
            return $this->data['datecreated'];
        }

        $dateCreated = $this->getAtomExtension()->getDateCreated();

        $this->data['datecreated'] = $dateCreated;

        return $this->data['datecreated'];
    }

    /**
     * Get the entry modification date
     *
     * @return null|DateTime
     */
    public function getDateModified()
    {
        if (array_key_exists('datemodified', $this->data)) {
            return $this->data['datemodified'];
        }

        $dateModified = $this->getAtomExtension()->getDateModified();

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

        $description = $this->getAtomExtension()->getDescription();

        $this->data['description'] = $description;

        return $this->data['description'];
    }

    /**
     * Get the entry enclosure
     *
     * @return null|object{href: string, length: int, type: string}
     */
    public function getEnclosure()
    {
        if (array_key_exists('enclosure', $this->data)) {
            return $this->data['enclosure'];
        }

        $enclosure = $this->getAtomExtension()->getEnclosure();

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

        $id = $this->getAtomExtension()->getId();

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

        $links = $this->getAtomExtension()->getLinks();

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

        $title = $this->getAtomExtension()->getTitle();

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

        $commentcount = $this->getThreadExtension()->getCommentCount();

        if (! $commentcount) {
            $commentcount = $this->getAtomExtension()->getCommentCount();
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

        $commentlink = $this->getAtomExtension()->getCommentLink();

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

        $commentfeedlink = $this->getAtomExtension()->getCommentFeedLink();

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

        $categoryCollection = $this->getAtomExtension()->getCategories();

        if (count($categoryCollection) === 0) {
            $categoryCollection = $this->getDublinCoreExtension()->getCategories();
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

        $source = $this->getAtomExtension()->getSource();

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

    private function getAtomExtension(): Entry
    {
        $extension = $this->getExtension('Atom');

        if (! $extension instanceof Entry) {
            throw new RuntimeException('Unable to retrieve Atom entry extension');
        }

        return $extension;
    }

    private function getDublinCoreExtension(): DublinCoreEntry
    {
        $extension = $this->getExtension('DublinCore');

        if (! $extension instanceof DublinCoreEntry) {
            throw new RuntimeException('Unable to retrieve DublinCore entry extension');
        }

        return $extension;
    }

    private function getThreadExtension(): ThreadEntry
    {
        $extension = $this->getExtension('Thread');

        if (! $extension instanceof ThreadEntry) {
            throw new RuntimeException('Unable to retrieve Thread entry extension');
        }

        return $extension;
    }
}
