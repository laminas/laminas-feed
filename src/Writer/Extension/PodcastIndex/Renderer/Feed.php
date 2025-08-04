<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex\Renderer;

use DateTimeInterface;
use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension;
use Laminas\Feed\Writer\Feed as FeedWriter;

/**
 * Renders PodcastIndex data of a RSS Feed
 *
 * @psalm-import-type PersonArray from \Laminas\Feed\Writer\Extension\PodcastIndex\Feed
 * @psalm-import-type UpdateFrequencyArray from \Laminas\Feed\Writer\Extension\PodcastIndex\Feed
 * @psalm-import-type TrailerArray from \Laminas\Feed\Writer\Extension\PodcastIndex\Feed
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
}
