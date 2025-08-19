<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex\Renderer;

use DateTimeInterface;
use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension;
use Laminas\Feed\Writer\Feed as FeedWriter;

use function number_format;

/**
 * Renders PodcastIndex data of a RSS Feed
 *
 * @psalm-import-type PersonArray from \Laminas\Feed\Writer\Extension\PodcastIndex\Feed
 * @psalm-import-type UpdateFrequencyArray from \Laminas\Feed\Writer\Extension\PodcastIndex\Feed
 * @psalm-import-type TrailerArray from \Laminas\Feed\Writer\Extension\PodcastIndex\Feed
 * @psalm-import-type RemoteItemArray from \Laminas\Feed\Writer\Extension\PodcastIndex\Feed
 * @psalm-import-type ValueRecipientArray from \Laminas\Feed\Writer\Extension\PodcastIndex\Feed
 * @psalm-import-type ValueArray from \Laminas\Feed\Writer\Extension\PodcastIndex\Feed
 */
class Feed extends Extension\AbstractRenderer
{
    /**
     * Set to TRUE if a rendering method actually renders something. This
     * is used to prevent premature appending of a XML namespace declaration
     * until an element which requires it is actually appended.
     *
     * @var bool
     */
    protected $called = false;

    /**
     * Render feed
     */
    public function render(): void
    {
        $this->setLocked($this->dom, $this->base);
        $this->setFunding($this->dom, $this->base);
        $this->setLicense($this->dom, $this->base);
        $this->setLocation($this->dom, $this->base);
        $this->setImages($this->dom, $this->base);
        $this->setUpdateFrequency($this->dom, $this->base);
        $this->setPeople($this->dom, $this->base);
        $this->setTrailer($this->dom, $this->base);
        $this->setGuid($this->dom, $this->base);
        $this->setMedium($this->dom, $this->base);
        $this->setBlocks($this->dom, $this->base);
        $this->setTxts($this->dom, $this->base);
        $this->setPodping($this->dom, $this->base);
        $this->setRemoteItems($this->dom, $this->base);
        $this->setPodroll($this->dom, $this->base);
        $this->setPublisher($this->dom, $this->base);
        $this->setValues($this->dom, $this->base);
        if ($this->called) {
            $this->_appendNamespaces();
        }
    }

    /**
     * Append feed namespaces
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _appendNamespaces(): void
    {
        $this->getRootElement()->setAttribute(
            'xmlns:podcast',
            'https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/1.0.md'
        );
    }

    /**
     * Set feed lock
     */
    protected function setLocked(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var null|array<string, string> $locked */
        $locked = $container->getPodcastIndexLocked();
        if ($locked === null) {
            return;
        }
        $el   = $dom->createElement('podcast:locked');
        $text = $dom->createTextNode((string) $locked['value']);
        $el->appendChild($text);
        $el->setAttribute('owner', $locked['owner']);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed funding
     */
    protected function setFunding(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var null|array<string, string> $funding */
        $funding = $container->getPodcastIndexFunding();
        if ($funding === null) {
            return;
        }
        $el   = $dom->createElement('podcast:funding');
        $text = $dom->createTextNode((string) $funding['title']);
        $el->appendChild($text);
        $el->setAttribute('url', $funding['url']);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed license
     */
    private function setLicense(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var null|array{identifier: string, url: string} $license */
        $license = $container->getPodcastIndexLicense();
        if ($license === null) {
            return;
        }
        $el   = $dom->createElement('podcast:license');
        $text = $dom->createTextNode($license['identifier']);
        $el->appendChild($text);
        $el->setAttribute('url', $license['url']);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed location
     */
    private function setLocation(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var null|array{description: string, geo?: string, osm?: string} $location */
        $location = $container->getPodcastIndexLocation();
        if ($location === null) {
            return;
        }
        $el   = $dom->createElement('podcast:location');
        $text = $dom->createTextNode($location['description']);
        $el->appendChild($text);
        if (isset($location['geo']) && $location['geo'] !== '') {
            $el->setAttribute('geo', $location['geo']);
        }
        if (isset($location['osm']) && $location['osm'] !== '') {
            $el->setAttribute('osm', $location['osm']);
        }
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed images
     */
    private function setImages(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var null|array{srcset: string} $images */
        $images = $container->getPodcastIndexImages();
        if ($images === null) {
            return;
        }
        $el = $dom->createElement('podcast:images');
        $el->setAttribute('srcset', $images['srcset']);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed update frequency
     */
    private function setUpdateFrequency(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var null|UpdateFrequencyArray $updateFrequency */
        $updateFrequency = $container->getPodcastIndexUpdateFrequency();
        if ($updateFrequency === null) {
            return;
        }
        $el   = $dom->createElement('podcast:updateFrequency');
        $text = $dom->createTextNode($updateFrequency['description']);
        $el->appendChild($text);
        if (($updateFrequency['complete'] ?? null) === true) {
            $el->setAttribute('complete', 'true');
        }
        if (isset($updateFrequency['dtstart'])) {
            $el->setAttribute('dtstart', $updateFrequency['dtstart']->format(DateTimeInterface::ATOM));
        }
        if (isset($updateFrequency['rrule']) && $updateFrequency['rrule'] !== '') {
            $el->setAttribute('rrule', $updateFrequency['rrule']);
        }
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed people
     */
    private function setPeople(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var null|list<PersonArray> $people */
        $people = $container->getPodcastIndexPeople();
        if ($people === null || $people === []) {
            return;
        }
        foreach ($people as $person) {
            $el   = $dom->createElement('podcast:person');
            $text = $dom->createTextNode($person['name']);
            $el->appendChild($text);

            if (isset($person['role']) && $person['role'] !== '') {
                $el->setAttribute('role', $person['role']);
            }
            if (isset($person['group']) && $person['group'] !== '') {
                $el->setAttribute('group', $person['group']);
            }
            if (isset($person['img']) && $person['img'] !== '') {
                $el->setAttribute('img', $person['img']);
            }
            if (isset($person['href']) && $person['href'] !== '') {
                $el->setAttribute('href', $person['href']);
            }
            $root->appendChild($el);
        }
        $this->called = true;
    }

    /**
     * Set feed trailer
     */
    private function setTrailer(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var null|TrailerArray $trailer */
        $trailer = $container->getPodcastIndexTrailer();
        if ($trailer === null) {
            return;
        }
        $el   = $dom->createElement('podcast:trailer');
        $text = $dom->createTextNode($trailer['title']);
        $el->appendChild($text);
        $el->setAttribute('pubdate', $trailer['pubdate']);
        $el->setAttribute('url', $trailer['url']);
        if (isset($trailer['length'])) {
            $el->setAttribute('length', (string) $trailer['length']);
        }
        if (isset($trailer['type'])) {
            $el->setAttribute('type', $trailer['type']);
        }
        if (isset($trailer['season'])) {
            $el->setAttribute('season', (string) $trailer['season']);
        }
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed guid
     */
    private function setGuid(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var null|array{value: string} $guid */
        $guid = $container->getPodcastIndexGuid();
        if ($guid === null) {
            return;
        }
        $el   = $dom->createElement('podcast:guid');
        $text = $dom->createTextNode($guid['value']);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed medium
     */
    private function setMedium(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var null|array{value: string} $medium */
        $medium = $container->getPodcastIndexMedium();
        if ($medium === null) {
            return;
        }
        $el   = $dom->createElement('podcast:medium');
        $text = $dom->createTextNode($medium['value']);
        $el->appendChild($text);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed blocks
     */
    private function setBlocks(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var list<array{value: string, id?: string}>|null $blocks */
        $blocks = $container->getPodcastIndexBlocks();
        if ($blocks === null || $blocks === []) {
            return;
        }

        foreach ($blocks as $block) {
            $el   = $dom->createElement('podcast:block');
            $text = $dom->createTextNode($block['value']);
            $el->appendChild($text);
            if (isset($block['id']) && $block['id'] !== '') {
                $el->setAttribute('id', $block['id']);
            }
            $root->appendChild($el);
        }
        $this->called = true;
    }

    /**
     * Set feed txts
     */
    private function setTxts(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var list<array{value: string, purpose?: string}>|null $txts */
        $txts = $container->getPodcastIndexTxts();
        if ($txts === null || $txts === []) {
            return;
        }

        foreach ($txts as $txt) {
            $el   = $dom->createElement('podcast:txt');
            $text = $dom->createTextNode($txt['value']);
            $el->appendChild($text);
            if (isset($txt['purpose']) && $txt['purpose'] !== '') {
                $el->setAttribute('purpose', $txt['purpose']);
            }
            $root->appendChild($el);
        }
        $this->called = true;
    }

    /**
     * Set feed podping
     */
    private function setPodping(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var null|array{usesPodping: bool} $podping */
        $podping = $container->getPodcastIndexPodping();
        if ($podping === null) {
            return;
        }

        $usesPodping = $podping['usesPodping'] ? 'true' : 'false';

        $el = $dom->createElement('podcast:podping');
        $el->setAttribute('usesPodping', $usesPodping);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed remote items
     */
    private function setRemoteItems(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var list<RemoteItemArray>|null $remoteItems */
        $remoteItems = $container->getPodcastIndexRemoteItems();
        if ($remoteItems === null || $remoteItems === []) {
            return;
        }

        foreach ($remoteItems as $remoteItem) {
            $el = $this->createRemoteItemElement($dom, $remoteItem);
            $root->appendChild($el);
        }

        $this->called = true;
    }

    /**
     * Set podroll element with remote items
     */
    private function setPodroll(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var list<RemoteItemArray>|null $podrollItems */
        $podrollItems = $container->getPodcastIndexPodroll();
        if ($podrollItems === null || $podrollItems === []) {
            return;
        }

        $podroll = $dom->createElement('podcast:podroll');

        foreach ($podrollItems as $remoteItem) {
            $el = $this->createRemoteItemElement($dom, $remoteItem);
            $podroll->appendChild($el);
        }

        $root->appendChild($podroll);

        $this->called = true;
    }

    /**
     * Set publisher element with remote items
     */
    private function setPublisher(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var RemoteItemArray|null $publisherItem */
        $publisherItem = $container->getPodcastIndexPublisher();
        if ($publisherItem === null) {
            return;
        }

        $publisher = $dom->createElement('podcast:publisher');
        $el        = $this->createRemoteItemElement($dom, $publisherItem);
        $publisher->appendChild($el);
        $root->appendChild($publisher);

        $this->called = true;
    }

    /**
     * Create remote item element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param RemoteItemArray $remoteItem
     */
    private function createRemoteItemElement($dom, $remoteItem): DOMElement
    {
        $el = $dom->createElement('podcast:remoteItem');
        $el->setAttribute('feedGuid', $remoteItem['feedGuid']);

        if (isset($remoteItem['feedUrl']) && $remoteItem['feedUrl'] !== '') {
            $el->setAttribute('feedUrl', $remoteItem['feedUrl']);
        }
        if (isset($remoteItem['itemGuid']) && $remoteItem['itemGuid'] !== '') {
            $el->setAttribute('itemGuid', $remoteItem['itemGuid']);
        }
        if (isset($remoteItem['medium']) && $remoteItem['medium'] !== '') {
            $el->setAttribute('medium', $remoteItem['medium']);
        }
        if (isset($remoteItem['title']) && $remoteItem['title'] !== '') {
            $el->setAttribute('title', $remoteItem['title']);
        }

        return $el;
    }

    /**
     * Set values with the value recipients
     */
    private function setValues(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var FeedWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var list<ValueArray>|null $values */
        $values = $container->getPodcastIndexValues();
        if ($values === null || $values === []) {
            return;
        }

        foreach ($values as $value) {
            if (! isset($value['recipients'])) {
                continue;
            }
            // set value attributes
            $valueElement = $dom->createElement('podcast:value');
            $valueElement->setAttribute('type', $value['type']);
            $valueElement->setAttribute('method', $value['method']);
            if (isset($value['suggested'])) {
                // ensure float instead of scientific notation
                $suggested = number_format($value['suggested'], 11);
                $valueElement->setAttribute('suggested', $suggested);
            }
            // set value child nodes: recipients
            foreach ($value['recipients'] as $valueRecipient) {
                $recipientElement = $this->createValueRecipientElement($dom, $valueRecipient);
                $valueElement->appendChild($recipientElement);
            }
            $root->appendChild($valueElement);
        }

        $this->called = true;
    }

    /**
     * Create value recipient element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param ValueRecipientArray $valueRecipient
     */
    private function createValueRecipientElement($dom, $valueRecipient): DOMElement
    {
        $el = $dom->createElement('podcast:valueRecipient');
        if (isset($valueRecipient['name']) && $valueRecipient['name'] !== '') {
            $el->setAttribute('name', $valueRecipient['name']);
        }
        $el->setAttribute('type', $valueRecipient['type']);
        $el->setAttribute('address', $valueRecipient['address']);
        $el->setAttribute('split', (string) $valueRecipient['split']);

        if (isset($valueRecipient['customKey']) && $valueRecipient['customKey'] !== '') {
            $el->setAttribute('customKey', $valueRecipient['customKey']);
        }
        if (isset($valueRecipient['customValue']) && $valueRecipient['customValue'] !== '') {
            $el->setAttribute('customValue', $valueRecipient['customValue']);
        }
        if (isset($valueRecipient['fee'])) {
            $el->setAttribute('fee', (string) $valueRecipient['fee']);
        }

        return $el;
    }
}
