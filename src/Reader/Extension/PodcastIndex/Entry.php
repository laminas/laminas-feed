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
     *
     * @psalm-return null|TranscriptObject
     */
    public function getTranscript(): ?stdClass
    {
        if (array_key_exists('transcript', $this->data)) {
            return $this->data['transcript'];
        }

        $transcript = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:transcript');

        if ($nodeList instanceof DOMNodeList && $nodeList->length > 0) {
            /** @var DOMElement $node */
            $node                 = $nodeList->item(0);
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
     *
     * @psalm-return null|TranscriptObject
     */
    public function getPodcastIndexTranscript(): ?stdClass
    {
        return $this->getTranscript();
    }

    /**
     * Get the entry chapters
     *
     * @psalm-return null|ChaptersObject
     */
    public function getChapters(): ?stdClass
    {
        if (array_key_exists('chapters', $this->data)) {
            return $this->data['chapters'];
        }

        $chapters = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:chapters');

        if ($nodeList instanceof DOMNodeList && $nodeList->length > 0) {
            /** @var DOMElement $node */
            $node           = $nodeList->item(0);
            $chapters       = new stdClass();
            $chapters->url  = $node->getAttribute('url');
            $chapters->type = $node->getAttribute('type');
        }

        $this->data['chapters'] = $chapters;

        return $this->data['chapters'];
    }

    /**
     * Get the entry chapters
     *
     * @psalm-return null|ChaptersObject
     */
    public function getPodcastIndexChapters(): ?stdClass
    {
        return $this->getChapters();
    }

    /**
     * Get the entry soundbites
     *
     * @psalm-return array<SoundbiteObject>
     */
    public function getSoundbites(): array
    {
        if (array_key_exists('soundbites', $this->data)) {
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
     *
     * @psalm-return array<SoundbiteObject>
     */
    public function getPodcastIndexSoundbites(): array
    {
        return $this->getSoundbites();
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
