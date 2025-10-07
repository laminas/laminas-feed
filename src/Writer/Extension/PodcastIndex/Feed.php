<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex;

use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Exception\InvalidArgumentException;
use Laminas\Stdlib\StringUtils;
use Laminas\Stdlib\StringWrapper\StringWrapperInterface;

use function array_key_exists;
use function count;
use function ctype_alpha;
use function is_string;
use function lcfirst;
use function method_exists;
use function rtrim;
use function strlen;
use function substr;
use function ucfirst;

/**
 * Describes PodcastIndex data of a RSS Feed
 *
 * @psalm-import-type FundingArray from Validator
 * @psalm-import-type LicenseArray from Validator
 * @psalm-import-type LocationArray from Validator
 * @psalm-import-type BlockArray from Validator
 * @psalm-import-type TxtArray from Validator
 * @psalm-import-type PersonArray from Validator
 * @psalm-import-type UpdateFrequencyArray from Validator
 * @psalm-import-type TrailerArray from Validator
 * @psalm-import-type RemoteItemArray from Validator
 * @psalm-import-type ValueRecipientArray from Validator
 * @psalm-import-type ValueArray from Validator
 * @psalm-import-type ImagesArray from Validator
 * @psalm-import-type DetailedImageArray from Validator
 * @psalm-import-type SocialInteractArray from Validator
 * @psalm-import-type LiveItemArray from Validator
 * @psalm-import-type ChatArray from Validator
 */
class Feed
{
    /**
     * Array of Feed data for rendering by Extension's renderers
     *
     * @var array
     */
    protected $data = [];

    /**
     * Contains all live item objects
     *
     * @var array<int, LiveItem>
     */
    protected $liveItems = [];

    /**
     * A pointer for the iterator to keep track of the live items array
     *
     * @var int
     */
    protected $liveItemKey = 0;

    /**
     * Encoding of all text values
     *
     * @var string
     */
    protected $encoding = 'UTF-8';

    /**
     * The used string wrapper supporting encoding
     *
     * @var StringWrapperInterface
     */
    protected $stringWrapper;

    public function __construct()
    {
        $this->stringWrapper = StringUtils::getWrapper($this->encoding);
    }

    /**
     * Set feed encoding
     */
    public function setEncoding(string $enc): Feed
    {
        $this->stringWrapper = StringUtils::getWrapper($enc);
        $this->encoding      = $enc;
        return $this;
    }

    /**
     * Get feed encoding
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Set a locked value of "yes" or "no" with an "owner" field.
     *
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexLocked(array $value): Feed
    {
        if (! isset($value['value']) || ! isset($value['owner'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "locked" must be an array containing keys "value" and "owner"'
            );
        }
        if (
            ! is_string($value['value'])
            || ! ctype_alpha($value['value']) && strlen($value['value']) > 0
        ) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "value" of "locked" may only contain alphabetic characters'
            );
        }
        $this->data['locked'] = $value;
        return $this;
    }

    /**
     * Sets a single feed funding tag.
     *
     * @deprecated Use `setPodcastIndexFundings()` or `addPodcastIndexFunding()` instead.
     *
     * @param FundingArray $value
     * @return $this
     */
    public function setPodcastIndexFunding(array $value): Feed
    {
        Validator::validateFunding($value);
        $this->data['funding'] = $value;
        return $this;
    }

    /**
     * Adds a feed funding tag.
     *
     * @param FundingArray $value
     * @return $this
     */
    public function addPodcastIndexFunding(array $value): Feed
    {
        Validator::validateFunding($value);
        if (! isset($this->data['fundings'])) {
            $this->data['fundings'] = [];
        }
        /** @var list<FundingArray> $this->data['fundings'] */
        $this->data['fundings'][] = $value;
        return $this;
    }

    /**
     * Set multiple funding tags
     *
     * @param list<FundingArray> $values
     * @return $this
     */
    public function setPodcastIndexFundings(array $values = []): self
    {
        $this->data['fundings'] = [];
        foreach ($values as $value) {
            $this->addPodcastIndexFunding($value);
        }
        return $this;
    }

    /**
     * Set feed license
     *
     * @param LicenseArray $value
     * @return $this
     */
    public function setPodcastIndexLicense(array $value): self
    {
        Validator::validateLicense($value);
        $this->data['license'] = $value;
        return $this;
    }

    /**
     * Set feed location
     *
     * @param LocationArray $value
     * @return $this
     */
    public function setPodcastIndexLocation(array $value): self
    {
        Validator::validateLocation($value);
        $this->data['location'] = $value;
        return $this;
    }

    /**
     * Sets a single `images` element with a srcset value.
     * _Note: The namespace `images` is deprecated in PodcastIndex.
     * Instead, you may set one or more `image` tags using the `setPodcastIndexDetailedImages()` method._
     *
     * @deprecated
     *
     * @param ImagesArray $value
     * @return $this
     */
    public function setPodcastIndexImages(array $value): self
    {
        Validator::validateImages($value);
        $this->data['images'] = $value;
        return $this;
    }

    /**
     * Adds a feed `image` element.
     *
     * @param DetailedImageArray $value
     * @return $this
     */
    public function addPodcastIndexDetailedImage(array $value): self
    {
        Validator::validateDetailedImage($value);

        if (! isset($this->data['detailedImages'])) {
            $this->data['detailedImages'] = [];
        }

        /** @var list<DetailedImageArray> $this->data['detailedImages'] */
        $this->data['detailedImages'][] = $value;
        return $this;
    }

    /**
     * Sets multiple feed `image` elements.
     * If no argument is passed, all existing image entries are removed.
     *
     * @param list<DetailedImageArray> $values
     * @return $this
     */
    public function setPodcastIndexDetailedImages(array $values = []): self
    {
        $this->data['detailedImages'] = [];
        foreach ($values as $value) {
            $this->addPodcastIndexDetailedImage($value);
        }
        return $this;
    }

    /**
     * Set feed update frequency
     *
     * @param UpdateFrequencyArray $value
     * @return $this
     */
    public function setPodcastIndexUpdateFrequency(array $value): self
    {
        Validator::validateUpdateFrequency($value);
        $this->data['updateFrequency'] = $value;
        return $this;
    }

    /**
     * Add feed person
     *
     * @psalm-param PersonArray $value
     * @return $this
     */
    public function addPodcastIndexPerson(array $value): self
    {
        Validator::validatePerson($value);

        if (! isset($this->data['people'])) {
            $this->data['people'] = [];
        }

        /** @var list<PersonArray> $this->data['people'] */
        $this->data['people'][] = $value;
        return $this;
    }

    /**
     * Set a new array of people.
     * If no argument is passed, all existing person entries are removed.
     *
     * @psalm-param list<PersonArray> $values
     * @return $this
     */
    public function setPodcastIndexPeople(array $values = []): self
    {
        $this->data['people'] = [];

        foreach ($values as $value) {
            $this->addPodcastIndexPerson($value);
        }
        return $this;
    }

    /**
     * Set a new array of persons. (alias of setPodcastIndexPeople)
     *  If no argument is passed, all existing person entries are removed.
     *
     * @psalm-param list<PersonArray> $values
     * @return $this
     */
    public function setPodcastIndexPersons(array $values = []): self
    {
        return $this->setPodcastIndexPeople($values);
    }

    /**
     * Set feed trailer
     *
     * @param TrailerArray $value
     * @return $this
     */
    public function setPodcastIndexTrailer(array $value): self
    {
        Validator::validateTrailer($value);
        $this->data['trailer'] = $value;
        return $this;
    }

    /**
     * Set feed guid
     *
     * @param array{value: string} $value
     * @return $this
     */
    public function setPodcastIndexGuid(array $value): self
    {
        Validator::validateGuid($value);
        $this->data['guid'] = $value;
        return $this;
    }

    /**
     * Set feed medium
     *
     * @param array{value: string} $value
     * @return $this
     */
    public function setPodcastIndexMedium(array $value): self
    {
        Validator::validateMedium($value);
        $this->data['medium'] = $value;
        return $this;
    }

    /**
     * Add feed block
     *
     * @param BlockArray $value
     * @return $this
     */
    public function addPodcastIndexBlock(array $value): self
    {
        Validator::validateBlock($value);

        if (! isset($this->data['blocks'])) {
            $this->data['blocks'] = [];
        }

        /** @var list<BlockArray> $this->data['blocks'] */
        $this->data['blocks'][] = $value;
        return $this;
    }

    /**
     * Set a new array of blocks.
     * If no argument is passed, it will just remove all existing block entries.
     *
     * @psalm-param list<BlockArray> $values
     * @return $this
     */
    public function setPodcastIndexBlocks(array $values = []): self
    {
        $this->data['blocks'] = [];

        foreach ($values as $value) {
            $this->addPodcastIndexBlock($value);
        }
        return $this;
    }

    /**
     * Add feed txt
     *
     * @param TxtArray $value
     * @return $this
     */
    public function addPodcastIndexTxt(array $value): self
    {
        Validator::validateTxt($value);

        if (! isset($this->data['txts'])) {
            $this->data['txts'] = [];
        }

        /** @var list<TxtArray> $this->data['txts'] */
        $this->data['txts'][] = $value;
        return $this;
    }

    /**
     * Set a new array of txts.
     * If no argument is passed, it will just remove all existing txt entries.
     *
     * @psalm-param list<TxtArray> $values
     * @return $this
     */
    public function setPodcastIndexTxts(array $values = []): self
    {
        $this->data['txts'] = [];

        foreach ($values as $value) {
            $this->addPodcastIndexTxt($value);
        }
        return $this;
    }

    /**
     * Set feed podping
     *
     * @param array{usesPodping: bool} $value
     * @return $this
     */
    public function setPodcastIndexPodping(array $value): self
    {
        Validator::validatePodping($value);
        $this->data['podping'] = $value;
        return $this;
    }

    /**
     * Add a feed remote item.
     * The remote item will be treated as a direct child of the current channel element.
     * To create remote items as nested children of other elements, use their respective methods instead.
     *
     * @param RemoteItemArray $value
     * @return $this
     */
    public function addPodcastIndexRemoteItem(array $value): self
    {
        Validator::validateRemoteItem($value);

        if (! isset($this->data['remoteItems'])) {
            $this->data['remoteItems'] = [];
        }

        /** @var list<RemoteItemArray> $this->data['remoteItems'] */
        $this->data['remoteItems'][] = $value;

        return $this;
    }

    /**
     * Create a new set of remote items for the feed.
     * If no argument is passed, it will just remove all existing remote items of this feed.
     * The remote items will be treated as direct children of the current channel element.
     * If they should be treated as nested children of other elements, use their respective methods instead.
     *
     * @psalm-param list<RemoteItemArray> $values
     * @return $this
     */
    public function setPodcastIndexRemoteItems(array $values = []): self
    {
        $this->data['remoteItems'] = [];

        foreach ($values as $value) {
            $this->addPodcastIndexRemoteItem($value);
        }
        return $this;
    }

    /**
     * Set a podroll element with and array of remote items
     * that will be set as the podroll's child elements.
     * If no argument is passed, it will remove the entire podroll entry and all its nested remote items.
     *
     * @psalm-param list<RemoteItemArray> $values
     * @return $this
     */
    public function setPodcastIndexPodroll(array $values = []): self
    {
        $this->data['podroll'] = [];

        foreach ($values as $value) {
            Validator::validateRemoteItem($value);
            $this->data['podroll'][] = $value;
        }
        return $this;
    }

    /**
     * Add a remote item to the podroll element.
     *
     * @psalm-param RemoteItemArray $value
     * @return $this
     */
    public function addPodcastIndexPodrollRemoteItem(array $value): self
    {
        Validator::validateRemoteItem($value);

        if (! isset($this->data['podroll'])) {
            $this->data['podroll'] = [];
        }

        /** @var list<RemoteItemArray> $this->data['podroll'] */
        $this->data['podroll'][] = $value;

        return $this;
    }

    /**
     * Set a publisher element.
     * It contains exactly one remote item as child element
     * and expects only an array of the remote item attributes.
     * If no argument is passed, any existing publisher entry will be removed.
     *
     * @psalm-param null|RemoteItemArray $value
     * @return $this
     */
    public function setPodcastIndexPublisher(?array $value = null): self
    {
        $this->data['publisher'] = [];

        if ($value && count($value) > 0) {
            Validator::validateRemoteItem($value);
            $this->data['publisher'] = $value;
        }

        return $this;
    }

    /**
     * Reset all value elements.
     * All value entries will be removed, including their nested valueRecipients.
     *
     * @return $this
     */
    public function resetPodcastIndexValues(): self
    {
        $this->data['values'] = [];
        return $this;
    }

    /**
     * Add a value element with one or more valueRecipients as children.
     * The method expects one array with the value attributes as first argument
     * and an array of arrays with the valueRecipients' attributes as second argument.
     *
     * @psalm-param ValueArray $value
     * @psalm-param list<ValueRecipientArray> $valueRecipients
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addPodcastIndexValue(array $value, array $valueRecipients): self
    {
        // validate the value attributes
        Validator::validateValue($value);

        // validate the valueRecipients array
        if (count($valueRecipients) < 1) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: the second argument of "value" must be an array 
                containing one or more "valueRecipients"'
            );
        }
        foreach ($valueRecipients as $valueRecipient) {
            Validator::validateValueRecipient($valueRecipient);
        }
        $value['valueRecipients'] = $valueRecipients;

        // add the values entry
        if (! isset($this->data['values'])) {
            $this->data['values'] = [];
        }

        /** @var list<ValueArray> $this->data['values'] */
        $this->data['values'][] = $value;

        return $this;
    }

    /**
     * Add a social interact for the feed.
     *
     * @param SocialInteractArray $value
     * @return $this
     */
    public function addPodcastIndexSocialInteract(array $value): self
    {
        Validator::validateSocialInteract($value);

        if (! isset($this->data['socialInteracts'])) {
            $this->data['socialInteracts'] = [];
        }

        /** @var list<SocialInteractArray> $this->data['socialInteracts'] */
        $this->data['socialInteracts'][] = $value;

        return $this;
    }

    /**
     * Create a new set of social interacts for the feed.
     * If no argument is passed, any existing social interact entry will be removed.
     *
     * @psalm-param list<SocialInteractArray> $values
     * @return $this
     */
    public function setPodcastIndexSocialInteracts(array $values = []): self
    {
        $this->data['socialInteracts'] = [];

        foreach ($values as $value) {
            $this->addPodcastIndexSocialInteract($value);
        }
        return $this;
    }

    /**
     * Creates a new Laminas\Feed\Writer\Extension\PodcastIndex\LiveItem data container for use.
     * This is NOT added to the current feed automatically, but is necessary to create a
     * container with some initial values preset based on the current feed data.
     *
     * @param LiveItemArray $value
     */
    public function createPodcastIndexLiveItem(array $value): LiveItem
    {
        Validator::validateLiveItem($value);
        $liveItem = new LiveItem($value);
        if ($this->getEncoding()) {
            $liveItem->setEncoding($this->getEncoding());
        }
        return $liveItem;
    }

    /**
     * Appends a Laminas\Feed\Writer\Extension\PodcastIndex\LiveItem object.
     *
     * @return $this
     */
    public function addPodcastIndexLiveItem(LiveItem $liveItem): self
    {
        $this->liveItems[] = $liveItem;
        return $this;
    }

    /**
     * Removes a specific indexed liveItem from the internal queue. LiveItems must be
     * added to a feed container in order to be indexed.
     *
     * @param  int $index
     * @return $this
     * @throws InvalidArgumentException
     */
    public function removePodcastIndexLiveItem($index)
    {
        if (! isset($this->liveItems[$index])) {
            throw new InvalidArgumentException('Undefined index: ' . $index . '. LiveItem does not exist.');
        }
        unset($this->liveItems[$index]);

        return $this;
    }

    /**
     * Set a chat element.
     * If no argument is passed, any existing chat entry will be removed.
     *
     * @psalm-param null|ChatArray $value
     * @return $this
     */
    public function setPodcastIndexChat(?array $value = null): self
    {
        $this->data['chat'] = [];

        if ($value && count($value) > 0) {
            Validator::validateChat($value);
            $this->data['chat'] = $value;
        }

        return $this;
    }

    /**
     * Overloading: proxy to internal setters
     *
     * @return mixed
     * @throws Writer\Exception\BadMethodCallException
     */
    public function __call(string $method, array $params)
    {
        $point = lcfirst(substr($method, 15));
        if (
            ! method_exists($this, 'setPodcastIndex' . ucfirst($point))
            && ! method_exists($this, 'addPodcastIndex' . ucfirst($point))
            && ! method_exists($this, 'addPodcastIndex' . rtrim(ucfirst($point), 's'))
        ) {
            throw new Writer\Exception\BadMethodCallException(
                'invalid method: ' . $method
            );
        }

        if (! array_key_exists($point, $this->data) || empty($this->data[$point])) {
            return;
        }
        return $this->data[$point];
    }

    /**
     * Is locked.
     * Specific get call for non-default naming.
     */
    public function isLocked(): bool
    {
        return $this->isPodcastIndexLocked();
    }

    /**
     * Is locked.
     * Specific get call for non-default naming.
     */
    public function isPodcastIndexLocked(): bool
    {
        if (isset($this->data['locked'], $this->data['locked']['value'])) {
            return $this->data['locked']['value'] === 'yes';
        }
        return false;
    }

    /**
     * Get lock owner.
     * Specific get call for non-default naming.
     */
    public function getLockOwner(): string|null
    {
        return $this->getPodcastIndexLockOwner();
    }

    /**
     * Get lock owner.
     * Specific get call for non-default naming.
     */
    public function getPodcastIndexLockOwner(): string|null
    {
        if (isset($this->data['locked'], $this->data['locked']['owner'])) {
            /** @psalm-var string $this->data['locked']['owner'] */
            return $this->data['locked']['owner'];
        }
        return null;
    }

    /**
     * Get persons.
     * Specific get call for non-default naming.
     */
    public function getPodcastIndexPersons(): array|null
    {
        /** @var list<PersonArray> $persons */
        $persons = $this->getPodcastIndexPeople();
        return $persons;
    }

    /**
     * Get live items.
     * Specific get call for non-default naming.
     */
    public function getPodcastIndexLiveItems(): array|null
    {
        if (count($this->liveItems) > 0) {
            return $this->liveItems;
        }
        return null;
    }

    /**
     * Get multiple funding tags
     * Specific get call for non-default naming.
     *
     * @return null|list<FundingArray>
     */
    public function getPodcastIndexFundings(): array|null
    {
        $fundings = null;
        if (isset($this->data['fundings'])) {
            /** @var list<FundingArray> $fundings */
            $fundings = $this->data['fundings'];
        }
        if (isset($this->data['funding'])) {
            /** @var FundingArray $single */
            $single     = $this->data['funding'];
            $fundings[] = $single;
        }
        return $fundings;
    }
}
