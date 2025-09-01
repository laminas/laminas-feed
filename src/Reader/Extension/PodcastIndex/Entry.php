<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Extension\PodcastIndex;

use DOMElement;
use DOMNodeList;
use Laminas\Feed\Reader\Extension;
use stdClass;

use function array_key_exists;

/**
 * Describes PodcastIndex data of an entry in a RSS Feed
 *
 * @psalm-import-type LicenseObject from AttributesReader
 * @psalm-import-type LocationObject from AttributesReader
 * @psalm-import-type BlockObject from AttributesReader
 * @psalm-import-type TxtObject from AttributesReader
 * @psalm-import-type PersonObject from AttributesReader
 * @psalm-import-type UpdateFrequencyObject from AttributesReader
 * @psalm-import-type TrailerObject from AttributesReader
 * @psalm-import-type RemoteItemObject from AttributesReader
 * @psalm-import-type ValueRecipientObject from AttributesReader
 * @psalm-import-type ValueObject from AttributesReader
 * @psalm-import-type ImageObject from AttributesReader
 * @psalm-import-type SocialInteractObject from AttributesReader
 * @psalm-import-type TranscriptObject from AttributesReader
 * @psalm-import-type ChaptersObject from AttributesReader
 * @psalm-import-type SoundbiteObject from AttributesReader
 */
class Entry extends Extension\AbstractEntry
{
    /**
     * Get the entry transcript
     */
    public function getTranscript(): ?stdClass
    {
        if (array_key_exists('transcript', $this->data)) {
            /** @psalm-var stdClass */
            return $this->data['transcript'];
        }

        $transcript = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:transcript');

        if ($nodeList->length > 0) {
            $node = $nodeList->item(0);
            assert($node instanceof DOMElement);
            $transcript           = new stdClass();
            $transcript->url      = $node->getAttribute('url');
            $transcript->type     = $node->getAttribute('type');
            $transcript->language = $node->getAttribute('language');
            $transcript->rel      = $node->getAttribute('rel');
        }

        $this->data['transcript'] = $transcript;

        return $this->data['transcript'];
    }

    /**
     * Get the entry transcript
     */
    public function getPodcastIndexTranscript(): ?stdClass
    {
        /** @psalm-var stdClass */
        return $this->getTranscript();
    }

    /**
     * Get the entry chapters
     */
    public function getChapters(): ?stdClass
    {
        if (array_key_exists('chapters', $this->data)) {
            /** @psalm-var stdClass */
            return $this->data['chapters'];
        }

        $chapters = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:chapters');

        if ($nodeList->length > 0) {
            $node = $nodeList->item(0);
            assert($node instanceof DOMElement);
            $chapters = new stdClass();
            $chapters->url = $node->getAttribute('url');
            $chapters->type = $node->getAttribute('type');
        }

        $this->data['chapters'] = $chapters;

        return $this->data['chapters'];
    }

    /**
     * Get the entry chapters
     */
    public function getPodcastIndexChapters(): ?stdClass
    {
        /** @psalm-var stdClass */
        return $this->getChapters();
    }

    /**
     * Get the entry soundbites
     */
    public function getSoundbites(): array
    {
        if (array_key_exists('soundbites', $this->data)) {
            /** @psalm-var array */
            return $this->data['soundbites'];
        }

        $soundbites = [];

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:soundbite');

        if ($nodeList->length > 0) {
            foreach ($nodeList as $node) {
                /** @var DOMElement $node */
                $soundbite            = new stdClass();
                $soundbite->title     = $node->nodeValue;
                $soundbite->startTime = $node->getAttribute('startTime');
                $soundbite->duration  = $node->getAttribute('duration');

                $soundbites[] = $soundbite;
            }
        }

        $this->data['soundbites'] = $soundbites;

        return $this->data['soundbites'];
    }

    /**
     * Get the entry soundbites
     */
    public function getPodcastIndexSoundbites(): array
    {
        /** @psalm-var array */
        return $this->getSoundbites();
    }

    /**
     * Get the entry location
     */
    public function getPodcastIndexLocation(): object|null
    {
        if (array_key_exists('location', $this->data)) {
            /** @psalm-var null|LocationObject */
            return $this->data['location'];
        }

        $location = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:location');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $location = AttributesReader::readLocation($item);
        }

        $this->data['location'] = $location;

        return $this->data['location'];
    }

    /**
     * Register PodcastIndex namespace
     */
    protected function registerNamespaces(): void
    {
        $this->xpath->registerNamespace(
            'podcast',
            'https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/1.0.md'
        );
    }
}
