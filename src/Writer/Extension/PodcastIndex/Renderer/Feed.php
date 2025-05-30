<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension;

/**
 * Renders PodcastIndex data of a RSS Feed
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
        $this->addPerson($this->dom, $this->base);
        $this->setPersons($this->dom, $this->base);
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
        /** @psalm-var null|array<string, string> $locked */
        $locked = $this->getDataContainer()->getPodcastIndexLocked();
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
        /** @psalm-var null|array<string, string> $funding */
        $funding = $this->getDataContainer()->getPodcastIndexFunding();
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
    protected function setLicense(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var null|array<string, string> $license */
        $license = $this->getDataContainer()->getPodcastIndexLicense();
        if ($license === null) {
            return;
        }
        $el   = $dom->createElement('podcast:license');
        $text = $dom->createTextNode((string) $license['identifier']);
        $el->appendChild($text);
        $el->setAttribute('url', $license['url']);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed location
     */
    protected function setLocation(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var null|array<string, string> $location */
        $location = $this->getDataContainer()->getPodcastIndexLocation();
        if ($location === null) {
            return;
        }
        $el   = $dom->createElement('podcast:location');
        $text = $dom->createTextNode((string) $location['description']);
        $el->appendChild($text);
        if (! empty($location['geo'])) {
            $el->setAttribute('geo', $location['geo']);
        }
        if (! empty($location['osm'])) {
            $el->setAttribute('osm', $location['osm']);
        }
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed images
     */
    protected function setImages(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var null|array<string, string> $images */
        $images = $this->getDataContainer()->getPodcastIndexImages();
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
    protected function setUpdateFrequency(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var null|array<string, mixed> $updateFrequency */
        $updateFrequency = $this->getDataContainer()->getPodcastIndexUpdateFrequency();
        if ($updateFrequency === null) {
            return;
        }
        $el   = $dom->createElement('podcast:updateFrequency');
        $text = $dom->createTextNode((string) $updateFrequency['description']);
        $el->appendChild($text);
        if (! empty($updateFrequency['complete'])) {
            $el->setAttribute('complete', $updateFrequency['complete']);
        }
        if (! empty($updateFrequency['dtstart'])) {
            $el->setAttribute('dtstart', $updateFrequency['dtstart']);
        }
        if (! empty($updateFrequency['rrule'])) {
            $el->setAttribute('rrule', $updateFrequency['rrule']);
        }
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Add feed person
     */
    protected function addPerson(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var null|array<string, mixed> $person */
        $persons = $this->getDataContainer()->getPodcastIndexPersons();
        if ($persons === null) {
            return;
        }
        foreach ($persons as $person) {
            $el   = $dom->createElement('podcast:person');
            $text = $dom->createTextNode((string) $person['name']);
            $el->appendChild($text);

            if (! empty($person['role'])) {
                $el->setAttribute('role', $person['role']);
            }
            if (! empty($person['group'])) {
                $el->setAttribute('group', $person['group']);
            }
            if (! empty($person['img'])) {
                $el->setAttribute('img', $person['img']);
            }
            if (! empty($person['href'])) {
                $el->setAttribute('href', $person['href']);
            }
            $root->appendChild($el);
        }
        $this->called = true;
    }

    protected function setPersons(DOMDocument $dom, DOMElement $root): void
    {
        $this->addPerson($dom, $root);
    }
}
