<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex;

use DateTimeInterface;
use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Extension\PodcastIndex\Validator;
use Laminas\Stdlib\StringUtils;
use Laminas\Stdlib\StringWrapper\StringWrapperInterface;

use function array_key_exists;
use function count;
use function ctype_alpha;
use function filter_var;
use function in_array;
use function is_bool;
use function is_int;
use function is_string;
use function lcfirst;
use function method_exists;
use function rtrim;
use function strlen;
use function substr;
use function ucfirst;

use const FILTER_VALIDATE_URL;

/**
 * Describes PodcastIndex data of a RSS Feed
 *
 * @psalm-import-type PersonArray from Validator
 * @psalm-import-type UpdateFrequencyArray from Validator
 * @psalm-import-type TrailerArray from Validator
 * @psalm-import-type RemoteItemArray from Validator
 * @psalm-import-type ValueRecipientArray from Validator
 * @psalm-import-type ValueArray from Validator
 * @psalm-import-type ImageArray from Validator
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
     * Set feed funding
     *
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexFunding(array $value): Feed
    {
        if (! isset($value['title']) || ! isset($value['url'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "funding" must be an array containing keys "title" and "url"'
            );
        }
        $this->data['funding'] = $value;
        return $this;
    }

    /**
     * Set feed license
     *
     * @param array{identifier: string, url: string} $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
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
     * @param array{description: string, geo?: string, osm?: string} $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexLocation(array $value): self
    {
        Validator::validateLocation($value);
        $this->data['location'] = $value;
        return $this;
    }

    /**
     * Set feed images srcset.
     * This method is deprecated, please use "addPodcastIndexImage" instead.
     *
     * @deprecated
     *
     * @param array{srcset: string} $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexImages(array $value): self
    {
        Validator::validateImages($value);
        $this->data['images'] = $value;
        return $this;
    }

    /**
     * Add feed image. Replaces "setPodcastIndexImages" method.
     *
     * @param ImageArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    /*public function addPodcastIndexImage(array $value): self
    {
        Validator::validateImage($value);

        if (! isset($this->data['images'])) {
            $this->data['images'] = [];
        }

        $this->data['images'][] = $value;
        return $this;
    }*/

    /**
     * Set feed update frequency
     *
     * @param UpdateFrequencyArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexUpdateFrequency(array $value): self
    {
        if (! isset($value['description'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "updateFrequency" must be an array containing at least the key "description"'
            );
        }
        if (! is_string($value['description'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "description" of "updateFrequency" must be of type string'
            );
        }
        if (isset($value['complete']) && ! is_bool($value['complete'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "complete" of "updateFrequency": must be of type boolean'
            );
        }
        if (isset($value['dtstart']) && ! $value['dtstart'] instanceof DateTimeInterface) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "dtstart" of "updateFrequency" must be of type DateTimeInterface'
            );
        }
        if (isset($value['rrule']) && ! is_string($value['rrule'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "rrule" of "updateFrequency" must be of type string'
            );
        }
        $this->data['updateFrequency'] = $value;
        return $this;
    }

    /**
     * Add feed person
     *
     * @psalm-param PersonArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
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
     * If no argument is passed, it will just remove all existing people.
     *
     * @psalm-param list<PersonArray> $values
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
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
     * If no argument is passed, it will just remove all existing persons.
     *
     * @psalm-param list<PersonArray> $values
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
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
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexTrailer(array $value): self
    {
        if (! isset($value['title']) || ! isset($value['pubdate']) || ! isset($value['url'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "trailer" must be an array containing the keys "title", "pubdate" and "url"'
            );
        }
        if (! is_string($value['title'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "title" of "trailer" must be of type string'
            );
        }
        if (! is_string($value['pubdate'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "pubdate" of "trailer" must be an RFC2822 formatted date string'
            );
        }
        /** @psalm-suppress DocblockTypeContradiction */
        if (! is_string($value['url']) || ! filter_var($value['url'], FILTER_VALIDATE_URL)) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "url" of "trailer" must be a url, starting with "http://" or "https://'
            );
        }
        if (isset($value['length']) && ! is_int($value['length'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "length" of "trailer": must be of type integer'
            );
        }
        if (isset($value['type']) && ! is_string($value['type'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "type" of "trailer" must be of type string'
            );
        }
        if (isset($value['season']) && ! is_int($value['season'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "season" of "trailer" must be of type integer'
            );
        }
        $this->data['trailer'] = $value;
        return $this;
    }

    /**
     * Set feed guid
     *
     * @param array{value: string} $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexGuid(array $value): self
    {
        if (! isset($value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "guid" must be an array containing the key "value"'
            );
        }
        if (! is_string($value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "value" of "guid" must be a UUIDv5 string'
            );
        }
        $this->data['guid'] = $value;
        return $this;
    }

    /**
     * Set feed medium
     *
     * @param array{value: string} $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexMedium(array $value): self
    {
        if (! isset($value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "medium" must be an array containing the key "value"'
            );
        }
        /** @psalm-suppress DocblockTypeContradiction */
        if (! is_string($value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "value" of "medium" must be a UUIDv5 string'
            );
        }
        $this->data['medium'] = $value;
        return $this;
    }

    /**
     * Add feed block
     *
     * @param array{value: string, id?: string} $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addPodcastIndexBlock(array $value): self
    {
        if (! isset($value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "block" must be an array containing the key "value"'
            );
        }
        if (! is_string($value['value']) || ! in_array($value['value'], ['yes', 'no'], true)) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "value" of "block" must be set to either "yes" or "no"'
            );
        }
        if (isset($value['id']) && ! is_string($value['id'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "id" of "block" must be of type string'
            );
        }

        if (! isset($this->data['blocks'])) {
            $this->data['blocks'] = [];
        }

        /** @var list<array{value: string, id?: string}> $this->data['blocks'] */
        $this->data['blocks'][] = $value;
        return $this;
    }

    /**
     * Set a new array of blocks.
     * If no argument is passed, it will just remove all existing block entries.
     *
     * @psalm-param list<array{value: string, id?: string}> $values
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
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
     * @param array{value: string, purpose?: string} $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     * @psalm-suppress DocblockTypeContradiction
     */
    public function addPodcastIndexTxt(array $value): self
    {
        Validator::validateTxt($value);

        if (! isset($this->data['txts'])) {
            $this->data['txts'] = [];
        }

        /** @var list<array{value: string, purpose?: string}> $this->data['txts'] */
        $this->data['txts'][] = $value;
        return $this;
    }

    /**
     * Set a new array of txts.
     * If no argument is passed, it will just remove all existing txt entries.
     *
     * @psalm-param list<array{value: string, purpose?: string}> $values
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
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
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexPodping(array $value): self
    {
        if (! isset($value['usesPodping'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "podping" must be an array containing the key "usesPodping"'
            );
        }
        if (! is_bool($value['usesPodping'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "usesPodping" of "podping" must be of type boolean'
            );
        }
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
        $this->validateRemoteItem($value);

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
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexPodroll(array $values = []): self
    {
        $this->data['podroll'] = [];

        foreach ($values as $value) {
            $this->validateRemoteItem($value);
            $this->data['podroll'][] = $value;
        }
        return $this;
    }

    /**
     * Add a remote item to the podroll parent element.
     *
     * @psalm-param RemoteItemArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addPodcastIndexPodrollRemoteItem(array $value): self
    {
        $this->validateRemoteItem($value);

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
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexPublisher(?array $value = null): self
    {
        $this->data['publisher'] = [];

        if ($value && count($value) > 0) {
            $this->validateRemoteItem($value);
            $this->data['publisher'] = $value;
        }

        return $this;
    }

    /**
     * Validate the values of the remote item.
     *
     * @param RemoteItemArray $value
     * @throws Writer\Exception\InvalidArgumentException
     * @psalm-suppress DocblockTypeContradiction
     */
    private function validateRemoteItem(array $value): void
    {
        if (! isset($value['feedGuid'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "remoteItem" must be an array containing at least the key "feedGuid"'
            );
        }
        if (! is_string($value['feedGuid'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "feedGuid" of "remoteItem" must be of type string'
            );
        }
        if (
            isset($value['feedUrl'])
            && (! is_string($value['feedUrl']) || ! filter_var($value['feedUrl'], FILTER_VALIDATE_URL))
        ) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "feedUrl" of "remoteItem" must be a url, starting with "http://" or "https://'
            );
        }
        if (isset($value['itemGuid']) && ! is_string($value['itemGuid'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "itemGuid" of "remoteItem" must be of type string'
            );
        }
        if (isset($value['medium']) && ! is_string($value['medium'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "medium" of "remoteItem" must be of type string'
            );
        }
        if (isset($value['title']) && ! is_string($value['title'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "title" of "remoteItem" must be of type string'
            );
        }
    }

    /**
     * Reset all value elements.
     * All value entries will be removed, including their nested value recipients.
     *
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function resetPodcastIndexValues(): self
    {
        $this->data['values'] = [];
        return $this;
    }

    /**
     * Add a value element with one or more value recipients as children.
     * The method expects one array with the value attributes as first argument
     * and an array of arrays with the value recipients' attributes as second argument.
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

        // validate the value recipients array
        if (count($valueRecipients) < 1) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: the second argument of "value" must an array containing at least one recipient'
            );
        }
        foreach ($valueRecipients as $recipient) {
            Validator::validateValueRecipient($recipient);
        }
        $value['recipients'] = $valueRecipients;

        // add the values entry
        if (! isset($this->data['values'])) {
            $this->data['values'] = [];
        }

        /** @var list<ValueArray> $this->data['values'] */
        $this->data['values'][] = $value;

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

    public function isLocked(): bool
    {
        return $this->isPodcastIndexLocked();
    }

    public function isPodcastIndexLocked(): bool
    {
        if (isset($this->data['locked'], $this->data['locked']['value'])) {
            return $this->data['locked']['value'] === 'yes';
        }
        return false;
    }

    public function getLockOwner(): string|null
    {
        return $this->getPodcastIndexLockOwner();
    }

    public function getPodcastIndexLockOwner(): string|null
    {
        if (isset($this->data['locked'], $this->data['locked']['owner'])) {
            return $this->data['locked']['owner'];
        }
        return null;
    }

    public function getPodcastIndexPersons(): array
    {
        return $this->getPodcastIndexPeople();
    }
}
