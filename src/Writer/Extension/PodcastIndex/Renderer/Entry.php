<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Entry as EntryWriter;
use Laminas\Feed\Writer\Extension;
use Laminas\Feed\Writer\Extension\PodcastIndex\Validator;

use function assert;

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
 * @psalm-import-type DetailedImageArray from Validator
 * @psalm-import-type SocialInteractArray from Validator
 * @psalm-import-type SeasonArray from Validator
 * @psalm-import-type EpisodeArray from Validator
 * @psalm-import-type SourceArray from Validator
 * @psalm-import-type IntegrityArray from Validator
 * @psalm-import-type AlternateEnclosureArray from Validator
 * @psalm-import-type ContentLinkArray from Validator
 * @psalm-import-type FundingArray from Validator
 * @psalm-import-type ChatArray from Validator
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
        $this->setLocations($this->dom, $this->base);
        $this->setLicense($this->dom, $this->base);
        $this->setPeople($this->dom, $this->base);
        $this->setTxts($this->dom, $this->base);
        $this->setSocialInteracts($this->dom, $this->base);
        $this->setValues($this->dom, $this->base);
        $this->setSeason($this->dom, $this->base);
        $this->setEpisode($this->dom, $this->base);
        $this->setAlternateEnclosures($this->dom, $this->base);
        $this->setDetailedImages($this->dom, $this->base);
        $this->setContentLinks($this->dom, $this->base);
        $this->setFundings($this->dom, $this->base);
        $this->setChat($this->dom, $this->base);
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

    private function getEntryWriter(): EntryWriter
    {
        $container = $this->getDataContainer();
        assert($container instanceof EntryWriter);

        return $container;
    }

    /**
     * Set entry transcript
     */
    protected function setTranscript(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var null|TranscriptArray $transcript */
        $transcript = $container->getPodcastIndexTranscript();
        if ($transcript === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $transcript, 'transcript');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set entry chapters
     */
    protected function setChapters(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var null|ChaptersArray $chapters */
        $chapters = $container->getPodcastIndexChapters();
        if ($chapters === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $chapters, 'chapters');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set entry soundbites
     */
    protected function setSoundbites(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var null|list<SoundbiteArray> $soundbites */
        $soundbites = $container->getPodcastIndexSoundbites();
        if (! $soundbites) {
            return;
        }
        foreach ($soundbites as $soundbite) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $soundbite, 'soundbite', 'title');
            $root->appendChild($el);
            $this->called = true;
        }
    }

    /**
     * Set multiple location tags
     */
    protected function setLocations(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var null|list<LocationArray> $locations */
        $locations = $container->getPodcastIndexLocations();
        if ($locations === null) {
            return;
        }

        foreach ($locations as $location) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $location, 'location', 'description');
            $root->appendChild($el);
        }

        $this->called = true;
    }

    /**
     * Set feed license
     */
    private function setLicense(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var null|LicenseArray $license */
        $license = $container->getPodcastIndexLicense();
        if ($license === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $license, 'license', 'identifier');

        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed people
     */
    private function setPeople(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var null|list<PersonArray> $people */
        $people = $container->getPodcastIndexPeople();
        if ($people === null || $people === []) {
            return;
        }
        foreach ($people as $person) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $person, 'person', 'name');
            $root->appendChild($el);
        }
        $this->called = true;
    }

    /**
     * Set entry txts
     */
    private function setTxts(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var list<TxtArray>|null $txts */
        $txts = $container->getPodcastIndexTxts();
        if ($txts === null || $txts === []) {
            return;
        }

        foreach ($txts as $txt) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $txt, 'txt', 'value');
            $root->appendChild($el);
        }
        $this->called = true;
    }

    /**
     * Set feed social interacts
     */
    private function setSocialInteracts(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var list<SocialInteractArray>|null $socialInteracts */
        $socialInteracts = $container->getPodcastIndexSocialInteracts();
        if ($socialInteracts === null || $socialInteracts === []) {
            return;
        }

        foreach ($socialInteracts as $socialInteract) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $socialInteract, 'socialInteract');
            $root->appendChild($el);
        }

        $this->called = true;
    }

    /**
     * Set the values
     */
    private function setValues(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var list<ValueArray>|null $values */
        $values = $container->getPodcastIndexValues();
        if ($values === null || $values === []) {
            return;
        }

        foreach ($values as $value) {
            if (! isset($value['valueRecipients'])) {
                continue;
            }
            $valueElement = ElementGenerator::createPodcastIndexElement($dom, $value, 'value');

            foreach ($value['valueRecipients'] as $recipient) {
                $recipientElement = ElementGenerator::createPodcastIndexElement($dom, $recipient, 'valueRecipient');
                $valueElement->appendChild($recipientElement);
            }

            if (isset($value['valueTimeSplits'])) {
                foreach ($value['valueTimeSplits'] as $split) {
                    $splitElement = ElementGenerator::createPodcastIndexElement($dom, $split, 'valueTimeSplit');

                    // set 1-n child nodes: valueRecipients
                    if (isset($split['valueRecipients'])) {
                        foreach ($split['valueRecipients'] as $recip) {
                            $element = ElementGenerator::createPodcastIndexElement($dom, $recip, 'valueRecipient');
                            $splitElement->appendChild($element);
                        }
                    }

                    // set 1 child node: value remote item
                    if (isset($split['remoteItem'])) {
                        $el = ElementGenerator::createPodcastIndexElement($dom, $split['remoteItem'], 'remoteItem');
                        $splitElement->appendChild($el);
                    }

                    $valueElement->appendChild($splitElement);
                }
            }
            $root->appendChild($valueElement);
        }

        $this->called = true;
    }

    /**
     * Set entry season
     */
    protected function setSeason(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var null|SeasonArray $season */
        $season = $container->getPodcastIndexSeason();
        if ($season === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $season, 'season', 'value');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set entry episode
     */
    protected function setEpisode(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var null|EpisodeArray $episode */
        $episode = $container->getPodcastIndexEpisode();
        if ($episode === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $episode, 'episode', 'value');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set the alternate enclosures
     */
    private function setAlternateEnclosures(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var list<AlternateEnclosureArray>|null $enclosures */
        $enclosures = $container->getPodcastIndexAlternateEnclosures();
        if ($enclosures === null || $enclosures === []) {
            return;
        }

        foreach ($enclosures as $enclosure) {
            if (! isset($enclosure['sources'])) {
                continue;
            }
            $enclosureElement = ElementGenerator::createPodcastIndexElement($dom, $enclosure, 'alternateEnclosure');
            foreach ($enclosure['sources'] as $source) {
                $el = ElementGenerator::createPodcastIndexElement($dom, $source, 'source');
                $enclosureElement->appendChild($el);
            }
            if (isset($enclosure['integrity'])) {
                    $el = ElementGenerator::createPodcastIndexElement($dom, $enclosure['integrity'], 'integrity');
                    $enclosureElement->appendChild($el);
            }
            $root->appendChild($enclosureElement);
        }

        $this->called = true;
    }

    /**
     * Set episode detailed images
     */
    private function setDetailedImages(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var list<DetailedImageArray>|null $detailedImages */
        $detailedImages = $container->getPodcastIndexDetailedImages();
        if ($detailedImages === null || $detailedImages === []) {
            return;
        }

        foreach ($detailedImages as $detailedImage) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $detailedImage, 'image');
            $root->appendChild($el);
        }

        $this->called = true;
    }

    /**
     * Set episode content links
     */
    private function setContentLinks(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var list<ContentLinkArray>|null $contentLinks */
        $contentLinks = $container->getPodcastIndexContentLinks();
        if ($contentLinks === null || $contentLinks === []) {
            return;
        }

        foreach ($contentLinks as $contentLink) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $contentLink, 'contentLink', 'description');
            $root->appendChild($el);
        }

        $this->called = true;
    }

    /**
     * Set episode funding
     */
    protected function setFundings(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var null|list<FundingArray> $fundings */
        $fundings = $container->getPodcastIndexFundings();
        if ($fundings === null) {
            return;
        }

        foreach ($fundings as $funding) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $funding, 'funding', 'title');
            $root->appendChild($el);
        }

        $this->called = true;
    }

    /**
     * Set chat element
     */
    private function setChat(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getEntryWriter();

        /** @psalm-var ChatArray|null $chat */
        $chat = $container->getPodcastIndexChat();
        if ($chat === null) {
            return;
        }

        $el = ElementGenerator::createPodcastIndexElement($dom, $chat, 'chat');
        $root->appendChild($el);
        $this->called = true;
    }
}
