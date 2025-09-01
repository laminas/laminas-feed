<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension;
use Laminas\Feed\Writer\Entry as EntryWriter;
use Laminas\Feed\Writer\Extension\PodcastIndex\Validator;
use function array_key_exists;

/**
 * Renders PodcastIndex data of an entry in a RSS Feed
 *
 * @psalm-import-type TranscriptArray from Validator
 * @psalm-import-type ChaptersArray from Validator
 * @psalm-import-type SoundbiteArray from Validator
 * @psalm-import-type LicenseArray from Validator
 * @psalm-import-type LocationArray from Validator
 * @psalm-import-type TxtArray from Validator
 * @psalm-import-type PersonArray from Validator
 * @psalm-import-type ValueRecipientArray from Validator
 * @psalm-import-type ValueArray from Validator
 * @psalm-import-type ImageArray from Validator
 */
class Entry extends Extension\AbstractRenderer
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
     * Render entry
     */
    public function render(): void
    {
        $this->setTranscript($this->dom, $this->base);
        $this->setChapters($this->dom, $this->base);
        $this->setSoundbites($this->dom, $this->base);
        $this->setLocation($this->dom, $this->base);
        if ($this->called) {
            $this->_appendNamespaces();
        }
    }

    /**
     * Append namespaces to entry root
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
     * Set entry transcript
     */
    protected function setTranscript(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var null|TranscriptArray $locked */
        $locked = $this->getDataContainer()->getPodcastIndexTranscript();
        if ($locked === null) {
            return;
        }
        $el = $dom->createElement('podcast:transcript');
        $el->setAttribute('url', $locked['url']);
        $el->setAttribute('type', $locked['type']);
        if (array_key_exists('language', $locked)) {
            $el->setAttribute('language', $locked['language']);
        }
        if (array_key_exists('rel', $locked)) {
            $el->setAttribute('rel', $locked['rel']);
        }
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set entry chapters
     */
    protected function setChapters(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var null|ChaptersArray $chapters */
        $chapters = $this->getDataContainer()->getPodcastIndexChapters();
        if ($chapters === null) {
            return;
        }
        $el = $dom->createElement('podcast:chapters');
        $el->setAttribute('url', $chapters['url']);
        $el->setAttribute('type', $chapters['type']);
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set entry soundbites
     */
    protected function setSoundbites(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var null|list<SoundbiteArray> $soundbites */
        $soundbites = $this->getDataContainer()->getPodcastIndexSoundbites();
        if (! $soundbites) {
            return;
        }
        foreach ($soundbites as $soundbite) {
            $el = $dom->createElement('podcast:soundbite');
            if (array_key_exists('title', $soundbite)) {
                $text = $dom->createTextNode((string) $soundbite['title']);
                $el->appendChild($text);
            }
            $el->setAttribute('startTime', $soundbite['startTime']);
            $el->setAttribute('duration', $soundbite['duration']);
            $root->appendChild($el);
            $this->called = true;
        }
    }

    /**
     * Set entry location
     */
    private function setLocation(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var null|LocationArray $location */
        $location = $this->getDataContainer()->getPodcastIndexLocation();
        if ($location === null) {
            return;
        }
        $el = ElementGenerator::createLocationElement($dom, $location);
        $root->appendChild($el);
        $this->called = true;
    }
    /**
     * Set feed license
     */
    private function setLicense(DOMDocument $dom, DOMElement $root): void
    {
        /** @psalm-var EntryWriter $container */
        $container = $this->getDataContainer();

        /** @psalm-var null|LicenseArray $license */
        $license = $container->getPodcastIndexLicense();
        if ($license === null) {
            return;
        }
        $el = ElementGenerator::createLicenseElement($dom, $license);

        $root->appendChild($el);
        $this->called = true;
    }
}
