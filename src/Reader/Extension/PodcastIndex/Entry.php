<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Extension\PodcastIndex;

use DOMElement;
use Laminas\Feed\Reader\Extension;
use stdClass;

use function array_key_exists;
use function assert;

/**
 * Describes PodcastIndex data of an entry in a RSS Feed
 *
 * @psalm-import-type FundingObject from AttributesReader
 * @psalm-import-type LicenseObject from AttributesReader
 * @psalm-import-type LocationObject from AttributesReader
 * @psalm-import-type BlockObject from AttributesReader
 * @psalm-import-type TxtObject from AttributesReader
 * @psalm-import-type PersonObject from AttributesReader
 * @psalm-import-type UpdateFrequencyObject from AttributesReader
 * @psalm-import-type TrailerObject from AttributesReader
 * @psalm-import-type RemoteItemObject from AttributesReader
 * @psalm-import-type ValueRecipientObject from AttributesReader
 * @psalm-import-type ValueTimeSplitObject from AttributesReader
 * @psalm-import-type ValueObject from AttributesReader
 * @psalm-import-type DetailedImageObject from AttributesReader
 * @psalm-import-type SocialInteractObject from AttributesReader
 * @psalm-import-type TranscriptObject from AttributesReader
 * @psalm-import-type ChaptersObject from AttributesReader
 * @psalm-import-type SoundbiteObject from AttributesReader
 * @psalm-import-type SeasonObject from AttributesReader
 * @psalm-import-type EpisodeObject from AttributesReader
 * @psalm-import-type SourceObject from AttributesReader
 * @psalm-import-type IntegrityObject from AttributesReader
 * @psalm-import-type AlternateEnclosureObject from AttributesReader
 * @psalm-import-type ContentLinkObject from AttributesReader
 * @psalm-import-type ChatObject from AttributesReader
 */
class Entry extends Extension\AbstractEntry
{
    /**
     * Get the entry transcript
     *
     * @return null|TranscriptObject
     */
    public function getTranscript(): ?stdClass
    {
        if (array_key_exists('transcript', $this->data)) {
            /** @psalm-var null|TranscriptObject */
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
     *
     * @return null|TranscriptObject
     */
    public function getPodcastIndexTranscript(): object|null
    {
        return $this->getTranscript();
    }

    /**
     * Get the entry chapters
     *
     * @return null|ChaptersObject
     */
    public function getChapters(): ?stdClass
    {
        if (array_key_exists('chapters', $this->data)) {
            /** @psalm-var null|ChaptersObject */
            return $this->data['chapters'];
        }

        $chapters = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:chapters');

        if ($nodeList->length > 0) {
            $node = $nodeList->item(0);
            assert($node instanceof DOMElement);
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
     * @return null|ChaptersObject
     */
    public function getPodcastIndexChapters(): object|null
    {
        return $this->getChapters();
    }

    /**
     * Get the entry soundbites
     *
     * @return list<SoundbiteObject>
     */
    public function getSoundbites(): array
    {
        if (array_key_exists('soundbites', $this->data)) {
            /** @psalm-var list<SoundbiteObject> */
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
        return $this->getSoundbites();
    }

    /**
     * Get the episode locations
     *
     * @psalm-return list<LocationObject>
     */
    public function getPodcastIndexLocations(): array
    {
        if (array_key_exists('locations', $this->data)) {
            /** @psalm-var list<LocationObject> */
            return $this->data['locations'];
        }

        $locations = [];

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:location');

        if ($nodeList->length > 0) {
            foreach ($nodeList as $entry) {
                assert($entry instanceof DOMElement);
                $location    = AttributesReader::readLocation($entry);
                $locations[] = $location;
            }
        }

        $this->data['locations'] = $locations;

        return $this->data['locations'];
    }

    /**
     * Get the entry license
     *
     * @return null|LicenseObject
     */
    public function getPodcastIndexLicense(): object|null
    {
        if (array_key_exists('license', $this->data)) {
            /** @psalm-var null|LicenseObject */
            return $this->data['license'];
        }

        $license = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:license');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $license = AttributesReader::readLicense($item);
        }

        $this->data['license'] = $license;

        return $this->data['license'];
    }

    /**
     * Get the entry people
     *
     * @return list<PersonObject>
     */
    public function getPodcastIndexPeople(): array
    {
        if (array_key_exists('people', $this->data)) {
            /** @psalm-var list<PersonObject> */
            return $this->data['people'];
        }

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:person');

        $personCollection = [];

        if ($nodeList->length > 0) {
            foreach ($nodeList as $entry) {
                assert($entry instanceof DOMElement);
                $person = AttributesReader::readPerson($entry);

                $personCollection[] = $person;
            }
        }

        $this->data['people'] = $personCollection;

        return $this->data['people'];
    }

    /**
     * Get the entry persons (alias of getPodcastIndexPeople)
     *
     * @return list<PersonObject>
     */
    public function getPodcastIndexPersons(): array
    {
        return $this->getPodcastIndexPeople();
    }

    /**
     * Get the entry txts
     *
     * @return list<TxtObject>
     */
    public function getPodcastIndexTxts(): array
    {
        if (array_key_exists('txts', $this->data)) {
            /** @psalm-var list<TxtObject> */
            return $this->data['txts'];
        }

        $txts = [];

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:txt');

        foreach ($nodeList as $entry) {
            assert($entry instanceof DOMElement);
            $object = AttributesReader::readTxt($entry);
            $txts[] = $object;
        }

        $this->data['txts'] = $txts;

        return $this->data['txts'];
    }

    /**
     * Get the entry social interacts
     *
     * @return list<SocialInteractObject>
     */
    public function getPodcastIndexSocialInteracts(): array
    {
        if (array_key_exists('socialInteracts', $this->data)) {
            /** @var list<SocialInteractObject> $socialInteracts */
            $socialInteracts = $this->data['socialInteracts'];
            return $socialInteracts;
        }

        $socialInteracts = [];

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:socialInteract');

        foreach ($nodeList as $entry) {
            assert($entry instanceof DOMElement);
            $object            = AttributesReader::readSocialInteract($entry);
            $socialInteracts[] = $object;
        }

        $this->data['socialInteracts'] = $socialInteracts;

        return $this->data['socialInteracts'];
    }

    /**
     * Get the entry values
     *
     * @return list<ValueObject>
     */
    public function getPodcastIndexValues(): array
    {
        if (array_key_exists('values', $this->data)) {
            /** @var list<ValueObject> $values */
            $values = $this->data['values'];
            return $values;
        }

        $values         = [];
        $valuesNodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:value');

        foreach ($valuesNodeList as $valueNode) {
            assert($valueNode instanceof DOMElement);
            $valueObject = AttributesReader::readValue($valueNode);

            $valueRecipientsNodeList = $this->xpath->query('podcast:valueRecipient', $valueNode);
            $valueRecipients         = [];

            foreach ($valueRecipientsNodeList as $entry) {
                assert($entry instanceof DOMElement);
                $object            = AttributesReader::readValueRecipient($entry);
                $valueRecipients[] = $object;
            }
            $valueObject->valueRecipients = $valueRecipients;

            $timeSplitsNodeList = $this->xpath->query('podcast:valueTimeSplit', $valueNode);
            if ($timeSplitsNodeList->length > 0) {
                $valueTimeSplits = [];
                foreach ($timeSplitsNodeList as $entry) {
                    assert($entry instanceof DOMElement);
                    $object            = $this->getValueTimeSplit($entry);
                    $valueTimeSplits[] = $object;
                }
                $valueObject->valueTimeSplits = $valueTimeSplits;
            }

            $values[] = $valueObject;
        }

        $this->data['values'] = $values;

        return $this->data['values'];
    }

    /**
     * Get value time split
     *
     * @return ValueTimeSplitObject
     */
    private function getValueTimeSplit(DOMElement $entry): object
    {
        $object = AttributesReader::readValueTimeSplit($entry);

        $itemsNodeList = $this->xpath->query('podcast:remoteItem', $entry);
        if ($itemsNodeList->length > 0) {
            assert($itemsNodeList[0] instanceof DOMElement);
            $itemsObject        = AttributesReader::readRemoteItem($itemsNodeList[0]);
            $object->remoteItem = $itemsObject;
        }

        $recipientsNodeList = $this->xpath->query('podcast:valueRecipient', $entry);
        if ($recipientsNodeList->length > 0) {
            $valueRecipients = [];
            foreach ($recipientsNodeList as $node) {
                assert($node instanceof DOMElement);
                $recipientObject   = AttributesReader::readValueRecipient($node);
                $valueRecipients[] = $recipientObject;
            }
            $object->valueRecipients = $valueRecipients;
        }

        return $object;
    }

    /**
     * Get the entry season
     *
     * @return null|SeasonObject
     */
    public function getPodcastIndexSeason(): object|null
    {
        if (array_key_exists('season', $this->data)) {
            /** @psalm-var SeasonObject */
            return $this->data['season'];
        }

        $season = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:season');

        if ($nodeList->length > 0) {
            $node = $nodeList->item(0);
            assert($node instanceof DOMElement);
            $season        = new stdClass();
            $season->value = $node->nodeValue;
            $season->name  = $node->getAttribute('name');
        }

        $this->data['season'] = $season;

        return $this->data['season'];
    }

    /**
     * Get the entry episode
     *
     * @return null|EpisodeObject
     */
    public function getPodcastIndexEpisode(): object|null
    {
        if (array_key_exists('episode', $this->data)) {
            /** @psalm-var EpisodeObject */
            return $this->data['episode'];
        }

        $episode = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:episode');

        if ($nodeList->length > 0) {
            $node = $nodeList->item(0);
            assert($node instanceof DOMElement);
            $episode          = new stdClass();
            $episode->value   = $node->nodeValue;
            $episode->display = $node->getAttribute('display');
        }

        $this->data['episode'] = $episode;

        return $this->data['episode'];
    }

    /**
     * Get the entry alternateEnclosures
     *
     * @return list<AlternateEnclosureObject>
     */
    public function getPodcastIndexAlternateEnclosures(): array
    {
        if (array_key_exists('alternateEnclosures', $this->data)) {
            /** @var list<AlternateEnclosureObject> $enclosures */
            $enclosures = $this->data['alternateEnclosures'];
            return $enclosures;
        }

        $enclosures         = [];
        $enclosuresNodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:alternateEnclosure');

        foreach ($enclosuresNodeList as $enclosureNode) {
            assert($enclosureNode instanceof DOMElement);
            $enclosureObject = AttributesReader::readAlternateEnclosure($enclosureNode);

            $sourcesNodeList = $this->xpath->query('podcast:source', $enclosureNode);
            $sources         = [];

            if ($sourcesNodeList->length > 0) {
                foreach ($sourcesNodeList as $entry) {
                    assert($entry instanceof DOMElement);
                    $object    = AttributesReader::readSource($entry);
                    $sources[] = $object;
                }
                $enclosureObject->sources = $sources;
            }

            $integrityNodeList = $this->xpath->query('podcast:integrity', $enclosureNode);
            if ($integrityNodeList->length > 0) {
                $node = $integrityNodeList->item(0);
                assert($node instanceof DOMElement);
                $integrity                  = AttributesReader::readIntegrity($node);
                $enclosureObject->integrity = $integrity;
            }

            $enclosures[] = $enclosureObject;
        }

        $this->data['alternateEnclosures'] = $enclosures;

        return $this->data['alternateEnclosures'];
    }

    /**
     * Get the episode detailed images.
     * Returns the contents of one or more `<podcast:image>` tags.
     *
     * @return list<DetailedImageObject>
     */
    public function getPodcastIndexDetailedImages(): array
    {
        if (array_key_exists('detailedImages', $this->data)) {
            /** @psalm-var list<DetailedImageObject> */
            return $this->data['detailedImages'];
        }

        $images = [];

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:image');

        if ($nodeList->length > 0) {
            foreach ($nodeList as $entry) {
                assert($entry instanceof DOMElement);
                $image    = AttributesReader::readDetailedImage($entry);
                $images[] = $image;
            }
        }

        $this->data['detailedImages'] = $images;

        return $this->data['detailedImages'];
    }

    /**
     * Get the episode content links.
     *
     * @psalm-return list<ContentLinkObject>
     */
    public function getPodcastIndexContentLinks(): array
    {
        if (array_key_exists('contentLinks', $this->data)) {
            /** @psalm-var list<ContentLinkObject> */
            return $this->data['contentLinks'];
        }

        $contentLinks = [];

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:contentLink');

        if ($nodeList->length > 0) {
            foreach ($nodeList as $entry) {
                assert($entry instanceof DOMElement);
                $contentLink    = AttributesReader::readContentLink($entry);
                $contentLinks[] = $contentLink;
            }
        }

        $this->data['contentLinks'] = $contentLinks;

        return $this->data['contentLinks'];
    }

    /**
     * Get the episode fundings
     *
     * @psalm-return list<FundingObject>
     */
    public function getPodcastIndexFundings(): array
    {
        if (array_key_exists('fundings', $this->data)) {
            /** @psalm-var list<FundingObject> */
            return $this->data['fundings'];
        }

        $fundings = [];

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:funding');

        if ($nodeList->length > 0) {
            foreach ($nodeList as $entry) {
                assert($entry instanceof DOMElement);
                $funding    = AttributesReader::readFunding($entry);
                $fundings[] = $funding;
            }
        }

        $this->data['fundings'] = $fundings;

        return $this->data['fundings'];
    }

    /**
     * Get the podcast chat
     *
     * @return null|ChatObject
     */
    public function getPodcastIndexChat(): object|null
    {
        if (array_key_exists('chat', $this->data)) {
            /** @psalm-var null|ChatObject */
            return $this->data['chat'];
        }

        $object   = null;
        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:chat');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $object = AttributesReader::readChat($item);
        }

        $this->data['chat'] = $object;
        return $this->data['chat'];
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
