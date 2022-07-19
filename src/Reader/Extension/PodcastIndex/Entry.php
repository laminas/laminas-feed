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
 */
class Entry extends Extension\AbstractEntry
{
    /**
     * Get the entry transcript
     *
     * @psalm-return null|object{url: string, type: string, language: string, rel: string}
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
     * Get the entry chapters
     *
     * @psalm-return null|object{url: string, type: string}
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
     * Get the entry soundbites
     *
     * @return stdClass[]
     * @psalm-return array<int, object{title: string, startTime: string, duration: string}>
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
