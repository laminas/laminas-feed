<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Extension\PodcastIndex;

use DOMElement;
use Laminas\Feed\Reader\Extension;
use Laminas\Feed\Reader\Extension\PodcastIndex\AttributesReader;
use stdClass;

use function array_key_exists;
use function assert;

/**
 * Describes PodcastIndex data of a RSS Feed
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
 */
class Feed extends Extension\AbstractFeed
{
    /**
     * Is the podcast locked (not available for indexing)?
     */
    public function isLocked(): bool
    {
        return $this->isPodcastIndexLocked();
    }

    /**
     * Is the podcast locked (not available for indexing)?
     */
    public function isPodcastIndexLocked(): bool
    {
        if (isset($this->data['locked'])) {
            return $this->data['locked'];
        }

        $locked = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/podcast:locked)');

        if (! $locked) {
            $locked = false;
        }

        $this->data['locked'] = $locked === 'yes';

        return $this->data['locked'];
    }

    /**
     * Get the owner of the podcast (for verification)
     */
    public function getLockOwner(): ?string
    {
        return $this->getPodcastIndexLockOwner();
    }

    /**
     * Get the owner of the podcast (for verification)
     */
    public function getPodcastIndexLockOwner(): ?string
    {
        if (isset($this->data['owner'])) {
            return $this->data['owner'];
        }

        $owner = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/podcast:locked/@owner)');

        if (! $owner) {
            $owner = null;
        }

        $this->data['owner'] = $owner;

        return $this->data['owner'];
    }

    /**
     * Get the entry funding link
     */
    public function getFunding(): ?stdClass
    {
        return $this->getPodcastIndexFunding();
    }

    /**
     * Get the entry funding link
     */
    public function getPodcastIndexFunding(): ?stdClass
    {
        if (array_key_exists('funding', $this->data)) {
            /** @psalm-var stdClass */
            return $this->data['funding'];
        }

        $funding = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:funding');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $funding        = new stdClass();
            $funding->url   = $item->getAttribute('url');
            $funding->title = $item->nodeValue;
        }

        $this->data['funding'] = $funding;

        return $this->data['funding'];
    }

    /**
     * Get the podcast license
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
     * Get the podcast location
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
     * Get the podcast images
     *
     * @psalm-return null|object{srcset: string}
     */
    public function getPodcastIndexImages(): object|null
    {
        if (array_key_exists('images', $this->data)) {
            /** @psalm-var null|object{srcset: string} */
            return $this->data['images'];
        }

        $images = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:images');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $images = AttributesReader::readImages($item);
        }

        $this->data['images'] = $images;

        return $this->data['images'];
    }

    /**
     * Get the podcast update frequency
     *
     * @psalm-return null|UpdateFrequencyObject
     */
    public function getPodcastIndexUpdateFrequency(): object|null
    {
        if (array_key_exists('updateFrequency', $this->data)) {
            /** @psalm-var null|UpdateFrequencyObject */
            return $this->data['updateFrequency'];
        }

        $updateFrequency = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:updateFrequency');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $updateFrequency = AttributesReader::readUpdateFrequency($item);
        }

        $this->data['updateFrequency'] = $updateFrequency;

        return $this->data['updateFrequency'];
    }

    /**
     * Get the podcast people
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
     * Get the podcast persons (alias of getPodcastIndexPeople)
     *
     * @psalm-return list<PersonObject>
     */
    public function getPodcastIndexPersons(): array
    {
        return $this->getPodcastIndexPeople();
    }

    /**
     * Get the podcast trailer
     *
     * @return null|TrailerObject
     */
    public function getPodcastIndexTrailer(): object|null
    {
        if (array_key_exists('trailer', $this->data)) {
            /** @psalm-var null|TrailerObject */
            return $this->data['trailer'];
        }

        $object = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:trailer');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $object = AttributesReader::readTrailer($item);
        }

        $this->data['trailer'] = $object;

        return $this->data['trailer'];
    }

    /**
     * Get the podcast guid
     *
     * @return null|object{value: string}
     */
    public function getPodcastIndexGuid(): object|null
    {
        if (array_key_exists('guid', $this->data)) {
            /** @psalm-var null|object{value: string} */
            return $this->data['guid'];
        }

        $object = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:guid');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $object = AttributesReader::readGuid($item);
        }

        $this->data['guid'] = $object;

        return $this->data['guid'];
    }

    /**
     * Get the podcast medium
     *
     * @return null|object{value: string}
     */
    public function getPodcastIndexMedium(): object|null
    {
        if (array_key_exists('medium', $this->data)) {
            /** @psalm-var null|object{value: string} */
            return $this->data['medium'];
        }

        $object = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:medium');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $object = AttributesReader::readMedium($item);
        }

        $this->data['medium'] = $object;

        return $this->data['medium'];
    }

    /**
     * Get the podcast blocks
     *
     * @return list<object{value: string, id?: string}>
     */
    public function getPodcastIndexBlocks(): array
    {
        if (array_key_exists('blocks', $this->data)) {
            /** @psalm-var list<object{value: string, id?: string}> */
            return $this->data['blocks'];
        }

        $blocks = [];

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:block');

        foreach ($nodeList as $entry) {
            assert($entry instanceof DOMElement);
            $object   = AttributesReader::readBlock($entry);
            $blocks[] = $object;
        }

        $this->data['blocks'] = $blocks;

        return $this->data['blocks'];
    }

    /**
     * Get the podcast txts
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
     * Get the podcast podping
     *
     * @return null|object{usesPodping: bool}
     */
    public function getPodcastIndexPodping(): object|null
    {
        if (array_key_exists('podping', $this->data)) {
            /** @psalm-var null|object{usesPodping: bool} */
            return $this->data['podping'];
        }

        $object = null;

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:podping');

        if ($nodeList->length > 0) {
            $item = $nodeList->item(0);
            assert($item instanceof DOMElement);
            $object              = new stdClass();
            $object->usesPodping = $item->getAttribute('usesPodping') === 'true';
        }

        $this->data['podping'] = $object;

        return $this->data['podping'];
    }

    /**
     * Get the podcast remoteItems
     *
     * @return list<RemoteItemObject>
     */
    public function getPodcastIndexRemoteItems(): array
    {
        if (array_key_exists('remoteItems', $this->data)) {
            /** @var list<RemoteItemObject> $remoteItems */
            $remoteItems = $this->data['remoteItems'];
            return $remoteItems;
        }

        $remoteItems = [];

        $nodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:remoteItem');

        foreach ($nodeList as $entry) {
            assert($entry instanceof DOMElement);
            $object        = AttributesReader::readRemoteItem($entry);
            $remoteItems[] = $object;
        }

        $this->data['remoteItems'] = $remoteItems;

        return $this->data['remoteItems'];
    }

    /**
     * Get the podcast podroll remote items
     *
     * @return list<RemoteItemObject>
     */
    public function getPodcastIndexPodroll(): array
    {
        if (array_key_exists('podroll', $this->data)) {
            /** @var list<RemoteItemObject> $podrollItems */
            $podrollItems = $this->data['podroll'];
            return $podrollItems;
        }

        $podrollItems    = [];
        $podrollNodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:podroll');

        if ($podrollNodeList->length > 0) {
            $podrollNode = $podrollNodeList->item(0);
            assert($podrollNode instanceof DOMElement);

            /** @psalm-suppress TooManyArguments */
            $remoteItems = $this->xpath->query('podcast:remoteItem', $podrollNode);
            foreach ($remoteItems as $entry) {
                assert($entry instanceof DOMElement);
                $object         = AttributesReader::readRemoteItem($entry);
                $podrollItems[] = $object;
            }
        }

        $this->data['podroll'] = $podrollItems;

        return $this->data['podroll'];
    }

    /**
     * Get the podcast publisher remote items
     *
     * @return RemoteItemObject|null
     */
    public function getPodcastIndexPublisher(): object|null
    {
        if (array_key_exists('publisher', $this->data)) {
            /** @var RemoteItemObject $publisherItem */
            $publisherItem = $this->data['publisher'];
            return $publisherItem;
        }

        $publisherItem     = null;
        $publisherNodeList = $this->xpath->query($this->getXpathPrefix() . '/podcast:publisher');

        if ($publisherNodeList->length > 0) {
            $publisherNode = $publisherNodeList->item(0);
            assert($publisherNode instanceof DOMElement);
            /** @psalm-suppress TooManyArguments */
            $remoteItemList = $this->xpath->query('podcast:remoteItem', $publisherNode);
            if ($remoteItemList->length > 0) {
                $remoteItem = $remoteItemList->item(0);
                assert($remoteItem instanceof DOMElement);
                $publisherItem = AttributesReader::readRemoteItem($remoteItem);
            }
        }

        $this->data['publisher'] = $publisherItem;

        return $this->data['publisher'];
    }

    /**
     * Get the podcast values
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
            $recipientsNodeList = $this->xpath->query('podcast:valueRecipient', $valueNode);
            $recipients         = [];

            foreach ($recipientsNodeList as $entry) {
                assert($entry instanceof DOMElement);
                $object       = AttributesReader::readValueRecipient($entry);
                $recipients[] = $object;
            }

            $valueObject->recipients = $recipients;
            $values[]                = $valueObject;
        }

        $this->data['values'] = $values;

        return $this->data['values'];
    }

    /**
     * Get the podcast social interacts
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
