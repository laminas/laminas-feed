<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Entry;

use DateTime;
use DateTimeInterface;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use Laminas\Feed\Reader;
use Laminas\Feed\Reader\Exception;
use Laminas\Feed\Reader\Exception\RuntimeException;
use Laminas\Feed\Reader\Extension\Atom\Entry as AtomEntry;
use Laminas\Feed\Reader\Extension\Content\Entry as ContentEntry;
use Laminas\Feed\Reader\Extension\DublinCore\Entry as DublinCoreEntry;
use Laminas\Feed\Reader\Extension\Slash\Entry as SlashEntry;
use Laminas\Feed\Reader\Extension\Thread\Entry as ThreadEntry;
use Laminas\Feed\Reader\Extension\WellFormedWeb\Entry as WellFormedWebEntry;
use stdClass;

use function array_key_exists;
use function count;
use function date_create_from_format;
use function in_array;
use function is_array;
use function is_string;
use function preg_match;
use function strtotime;
use function trim;

class Rss extends AbstractEntry implements EntryInterface
{
    /**
     * XPath query for RDF
     *
     * @var string
     */
    protected $xpathQueryRdf = '';

    /**
     * XPath query for RSS
     *
     * @var string
     */
    protected $xpathQueryRss = '';

    /**
     * @param string $entryKey
     * @param null|string $type
     */
    public function __construct(DOMElement $entry, $entryKey, $type = null)
    {
        parent::__construct($entry, $entryKey, $type);
        $this->xpathQueryRss = '//item[' . ($this->entryKey + 1) . ']';
        $this->xpathQueryRdf = '//rss:item[' . ($this->entryKey + 1) . ']';

        $manager    = Reader\Reader::getExtensionManager();
        $extensions = [
            'DublinCore\Entry',
            'Content\Entry',
            'Atom\Entry',
            'WellFormedWeb\Entry',
            'Slash\Entry',
            'Thread\Entry',
        ];
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
     * @return null|array
     */
    public function getAuthors()
    {
        if (array_key_exists('authors', $this->data)) {
            return $this->data['authors'];
        }

        $authors = [];

        $authorsDc = $this->getDublinCoreExtension()->getAuthors();
        if (! empty($authorsDc)) {
            foreach ($authorsDc as $author) {
                $authors[] = [
                    'name' => $author['name'],
                ];
            }
        }

        if (
            $this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $list = $this->xpath->query($this->xpathQueryRss . '//author');
        } else {
            $list = $this->xpath->query($this->xpathQueryRdf . '//rss:author');
        }
        if ($list instanceof DOMNodeList && $list->length) {
            foreach ($list as $author) {
                $string = trim($author->nodeValue);
                $data   = [];
                // Pretty rough parsing - but it's a catchall
                if (preg_match('/^.*@[^ ]*/', $string, $matches)) {
                    $data['email'] = trim($matches[0]);
                    if (preg_match('/\((.*)\)$/', $string, $matches)) {
                        $data['name'] = $matches[1];
                    }
                    $authors[] = $data;
                }
            }
        }

        if (count($authors) === 0) {
            $authors = $this->getAtomExtension()->getAuthors();
        } else {
            $authors = new Reader\Collection\Author(
                Reader\Reader::arrayUnique($authors)
            );
        }

        if (count($authors) === 0) {
            $authors = null;
        }

        $this->data['authors'] = $authors;

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

        $content = $this->getContentExtension()->getContent();

        if (empty($content)) {
            $content = $this->getDescription();
        }

        if (empty($content)) {
            $content = $this->getAtomExtension()->getContent();
        }

        $this->data['content'] = $content;

        return $this->data['content'];
    }

    /**
     * Get the entry's date of creation
     *
     * @return DateTime
     */
    public function getDateCreated()
    {
        return $this->getDateModified();
    }

    /**
     * Get the entry's date of modification
     *
     * @return DateTime
     * @throws Exception\RuntimeException
     */
    public function getDateModified()
    {
        if (array_key_exists('datemodified', $this->data)) {
            return $this->data['datemodified'];
        }

        $date = null;

        if (
            $this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $dateModified = $this->xpath->evaluate('string(' . $this->xpathQueryRss . '/pubDate)');
            if ($dateModified) {
                $dateModifiedParsed = strtotime($dateModified);
                if ($dateModifiedParsed) {
                    $date = new DateTime('@' . $dateModifiedParsed);
                } else {
                    $dateStandards = [
                        DateTime::RSS,
                        DateTime::RFC822,
                        DateTime::RFC2822,
                        null,
                    ];
                    foreach ($dateStandards as $standard) {
                        try {
                            $date = date_create_from_format($standard, $dateModified);
                            break;
                        } catch (\Exception $e) {
                            if ($standard === null) {
                                throw new Exception\RuntimeException(
                                    'Could not load date due to unrecognised format'
                                    . ' (should follow RFC 822 or 2822): ' . $e->getMessage(),
                                    0,
                                    $e
                                );
                            }
                        }
                    }
                }
            }
        }

        if (! $date instanceof DateTimeInterface) {
            $date = $this->getDublinCoreExtension()->getDate();
        }

        if (! $date instanceof DateTimeInterface) {
            $date = $this->getAtomExtension()->getDateModified();
        }

        if (! $date instanceof DateTimeInterface) {
            $date = null;
        }

        $this->data['datemodified'] = $date;

        return $this->data['datemodified'];
    }

    /**
     * Get the entry description
     *
     * @return null|string
     */
    public function getDescription()
    {
        if (array_key_exists('description', $this->data)) {
            return $this->data['description'];
        }

        $rssTypes = [
            Reader\Reader::TYPE_RSS_090,
            Reader\Reader::TYPE_RSS_10,
        ];

        $description = in_array($this->getType(), $rssTypes, true)
            ? $this->xpath->evaluate('string(' . $this->xpathQueryRdf . '/rss:description)')
            : $this->xpath->evaluate('string(' . $this->xpathQueryRss . '/description)');

        if (! $description) {
            $description = $this->getDublinCoreExtension()->getDescription();
        }

        if (empty($description)) {
            $description = $this->getAtomExtension()->getDescription();
        }

        $this->data['description'] = is_string($description) ? $description : null;

        return $this->data['description'];
    }

    /**
     * Get the entry enclosure
     *
     * @return null|object{url?: string, href?: string, length: int, type: string}
     */
    public function getEnclosure()
    {
        if (array_key_exists('enclosure', $this->data)) {
            return $this->data['enclosure'];
        }

        $enclosure = null;

        if ($this->getType() === Reader\Reader::TYPE_RSS_20) {
            $nodeList = $this->xpath->query($this->xpathQueryRss . '/enclosure');

            if ($nodeList instanceof DOMNodeList && $nodeList->length > 0) {
                /** @var DOMElement $node */
                $node              = $nodeList->item(0);
                $enclosure         = new stdClass();
                $enclosure->url    = $node->getAttribute('url');
                $enclosure->length = $node->getAttribute('length');
                $enclosure->type   = $node->getAttribute('type');
            }
        }

        if (! $enclosure) {
            $enclosure = $this->getAtomExtension()->getEnclosure();
        }

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

        $id = null;

        if (
            $this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $id = $this->xpath->evaluate('string(' . $this->xpathQueryRss . '/guid)');
        }

        if (! $id) {
            $id = $this->getDublinCoreExtension()->getId();
        }

        if (empty($id)) {
            $id = $this->getAtomExtension()->getId();
        }

        if (! $id) {
            if ($this->getPermalink()) {
                $id = $this->getPermalink();
            } elseif ($this->getTitle()) {
                $id = $this->getTitle();
            } else {
                $id = null;
            }
        }

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

        $links = [];

        if (
            $this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $list = $this->xpath->query($this->xpathQueryRss . '//link');
        } else {
            $list = $this->xpath->query($this->xpathQueryRdf . '//rss:link');
        }

        if ($list instanceof DOMNodeList && $list->length) {
            foreach ($list as $link) {
                $links[] = $link->nodeValue;
            }
        } else {
            $links = $this->getAtomExtension()->getLinks();
        }

        $this->data['links'] = $links;

        return $this->data['links'];
    }

    /**
     * Get all categories
     *
     * @return Reader\Collection\Category
     */
    public function getCategories()
    {
        if (array_key_exists('categories', $this->data)) {
            return $this->data['categories'];
        }

        if (
            $this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $list = $this->xpath->query($this->xpathQueryRss . '//category');
        } else {
            $list = $this->xpath->query($this->xpathQueryRdf . '//rss:category');
        }

        if ($list instanceof DOMNodeList && $list->length) {
            $categoryCollection = new Reader\Collection\Category();
            foreach ($list as $category) {
                $categoryCollection[] = [
                    'term'   => $category->nodeValue,
                    'scheme' => $category->getAttribute('domain'),
                    'label'  => $category->nodeValue,
                ];
            }
        } else {
            $categoryCollection = $this->getDublinCoreExtension()->getCategories();
        }

        if (count($categoryCollection) === 0) {
            $categoryCollection = $this->getAtomExtension()->getCategories();
        }

        $this->data['categories'] = $categoryCollection;

        return $this->data['categories'];
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

        $title = $this->getType() !== Reader\Reader::TYPE_RSS_10 && $this->getType() !== Reader\Reader::TYPE_RSS_090
            ? $this->xpath->evaluate('string(' . $this->xpathQueryRss . '/title)')
            : $this->xpath->evaluate('string(' . $this->xpathQueryRdf . '/rss:title)');

        if (! $title) {
            $title = $this->getDublinCoreExtension()->getTitle();
        }

        if (! $title) {
            $title = $this->getAtomExtension()->getTitle();
        }

        $this->data['title'] = is_string($title) ? $title : '';

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

        $commentcount = $this->getSlashExtension()->getCommentCount();

        if (! $commentcount) {
            $commentcount = $this->getThreadExtension()->getCommentCount();
        }

        if (! $commentcount) {
            $commentcount = $this->getAtomExtension()->getCommentCount();
        }

        if (! $commentcount) {
            $commentcount = 0;
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

        $commentlink = null;

        if (
            $this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $commentlink = $this->xpath->evaluate('string(' . $this->xpathQueryRss . '/comments)');
        }

        if (! $commentlink) {
            $commentlink = $this->getAtomExtension()->getCommentLink();
        }

        if (! $commentlink) {
            $commentlink = null;
        }

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

        $commentfeedlink = $this->getWellFormedWebExtension()->getCommentFeedLink();

        if (! $commentfeedlink) {
            $commentfeedlink = $this->getAtomExtension()->getCommentFeedLink('rss');
        }

        if (! $commentfeedlink) {
            $commentfeedlink = $this->getAtomExtension()->getCommentFeedLink('rdf');
        }

        if (! $commentfeedlink) {
            $commentfeedlink = null;
        }

        $this->data['commentfeedlink'] = $commentfeedlink;

        return $this->data['commentfeedlink'];
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

    private function getAtomExtension(): AtomEntry
    {
        $extension = $this->getExtension('Atom');
        if (! $extension instanceof AtomEntry) {
            throw new RuntimeException('Unable to load Atom entry extension');
        }
        return $extension;
    }

    private function getContentExtension(): ContentEntry
    {
        $extension = $this->getExtension('Content');
        if (! $extension instanceof ContentEntry) {
            throw new RuntimeException('Unable to load Content entry extension');
        }
        return $extension;
    }

    private function getDublinCoreExtension(): DublinCoreEntry
    {
        $extension = $this->getExtension('DublinCore');
        if (! $extension instanceof DublinCoreEntry) {
            throw new RuntimeException('Unable to load DublinCore entry extension');
        }
        return $extension;
    }

    private function getSlashExtension(): SlashEntry
    {
        $extension = $this->getExtension('Slash');
        if (! $extension instanceof SlashEntry) {
            throw new RuntimeException('Unable to load Slash entry extension');
        }
        return $extension;
    }

    private function getThreadExtension(): ThreadEntry
    {
        $extension = $this->getExtension('Thread');
        if (! $extension instanceof ThreadEntry) {
            throw new RuntimeException('Unable to load Thread entry extension');
        }
        return $extension;
    }

    private function getWellFormedWebExtension(): WellFormedWebEntry
    {
        $extension = $this->getExtension('WellFormedWeb');
        if (! $extension instanceof WellFormedWebEntry) {
            throw new RuntimeException('Unable to load WellFormedWeb entry extension');
        }
        return $extension;
    }
}
