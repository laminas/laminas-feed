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
            $chapters       = new stdClass();
            $chapters->url  = $node->getAttribute('url');
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
     * Get the entry license
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
     * @psalm-return list<PersonObject>
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
     * @psalm-return list<PersonObject>
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

            /** @psalm-suppress TooManyArguments */
            $valueRecipientsNodeList = $this->xpath->query('podcast:valueRecipient', $valueNode);
            $valueRecipients         = [];

            foreach ($valueRecipientsNodeList as $entry) {
                assert($entry instanceof DOMElement);
                $object            = AttributesReader::readValueRecipient($entry);
                $valueRecipients[] = $object;
            }
            $valueObject->valueRecipients = $valueRecipients;

            /** @psalm-suppress TooManyArguments */
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

            $values[]                     = $valueObject;
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

        /** @psalm-suppress TooManyArguments */
        $itemsNodeList = $this->xpath->query('podcast:remoteItem', $entry);
        if ($itemsNodeList->length > 0) {
            assert($itemsNodeList[0] instanceof DOMElement);
            $itemsObject        = AttributesReader::readRemoteItem($itemsNodeList[0]);
            $object->remoteItem = $itemsObject;
        }

        /** @psalm-suppress TooManyArguments */
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
