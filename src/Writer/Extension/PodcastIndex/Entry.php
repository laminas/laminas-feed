<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex;

use Laminas\Feed\Writer;
use Laminas\Stdlib\StringUtils;
use Laminas\Stdlib\StringWrapper\StringWrapperInterface;

use function array_key_exists;
use function is_numeric;
use function is_string;
use function lcfirst;
use function method_exists;
use function strlen;
use function substr;
use function ucfirst;

/**
 * Describes PodcastIndex data of an entry in a RSS Feed
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
 * @psalm-import-type SocialInteractArray from Validator
 */
class Entry
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
    public function setEncoding(string $enc): Entry
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
     * Set entry transcript
     *
     * @param TranscriptArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexTranscript(array $value): Entry
    {
        if (! isset($value['url']) || ! isset($value['type'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "transcript" must be an array containing keys'
                . ' "url" and "type" and optionally "language" and "rel"'
            );
        }
        $this->data['transcript'] = $value;
        return $this;
    }

    /**
     * Set entry chapters
     *
     * @param ChaptersArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexChapters(array $value): Entry
    {
        if (! isset($value['url']) || ! isset($value['type'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "chapters" must be an array containing keys "url" and "type"'
            );
        }
        $this->data['chapters'] = $value;
        return $this;
    }

    /**
     * Add entry soundbites
     *
     * @param list<SoundbiteArray> $values
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addPodcastIndexSoundbites(array $values): Entry
    {
        foreach ($values as $value) {
            $this->addPodcastIndexSoundbite($value);
        }

        return $this;
    }

    /**
     * Set entry soundbites.
     * If no argument is passed, the existing soundbite entries get removed.
     *
     * @param list<SoundbiteArray> $values
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexSoundbites(array $values = []): Entry
    {
        $this->data['soundbites'] = [];

        foreach ($values as $value) {
            $this->addPodcastIndexSoundbite($value);
        }

        return $this;
    }

    /**
     * Add entry soundbite
     *
     * @param SoundbiteArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addPodcastIndexSoundbite(array $value): Entry
    {
        if (! isset($value['startTime']) || ! isset($value['duration'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: any "soundbite" must be an array containing'
                . ' keys "startTime" and "duration" and optionally "title"'
            );
        }
        if (
            ! is_string($value['startTime'])
            || (! is_numeric($value['startTime']) && strlen($value['startTime']) > 0)
        ) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "startTime" of "soundbite" may only contain numeric characters and dots'
            );
        }
        if (
            ! is_string($value['duration'])
            || (! is_numeric($value['duration']) && strlen($value['duration']) > 0)
        ) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "duration" may only contain numeric characters and dots'
            );
        }
        if (! isset($this->data['soundbites'])) {
            $this->data['soundbites'] = [];
        }
        $this->data['soundbites'][] = $value;
        return $this;
    }

    /**
     * Set entry location
     *
     * @param LocationArray $value
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
     * Set entry license
     *
     * @param LicenseArray $value
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
     * Add entry person
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
     * Add entry txt
     *
     * @param TxtArray $value
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
     * Add a social interact for the entry.
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
     * Create a new set of social interacts for the entry.
     * If no argument is passed, existing social interacts will be removed.
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
                'invalid parameter: the second argument of "value" must be an array containing at least one recipient'
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
        if (
            ! array_key_exists($point, $this->data)
            || empty($this->data[$point])
        ) {
            return;
        }
        return $this->data[$point];
    }

    /**
     * Get persons.
     * Specific get call for non-default naming.
     */
    public function getPodcastIndexPersons(): array
    {
        /** @var list<PersonArray> $persons */
        $persons = $this->getPodcastIndexPeople();
        return $persons;
    }
}
