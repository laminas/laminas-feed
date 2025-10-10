<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex;

use Laminas\Feed\Writer;
use Laminas\Stdlib\StringUtils;
use Laminas\Stdlib\StringWrapper\StringWrapperInterface;

use function array_key_exists;
use function count;
use function is_numeric;
use function is_string;
use function lcfirst;
use function method_exists;
use function rtrim;
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
 * @psalm-import-type ValueTimeSplitArray from Validator
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
     * Add multiple entry soundbites
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
    public function setPodcastIndexSoundbites(array $values = []): self
    {
        $this->data['soundbites'] = [];

        foreach ($values as $value) {
            $this->addPodcastIndexSoundbite($value);
        }

        return $this;
    }

    /**
     * Add a single entry soundbite
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
     * Adds a feed location tag.
     *
     * @param LocationArray $value
     * @return $this
     */
    public function addPodcastIndexLocation(array $value): self
    {
        Validator::validateLocation($value);
        if (! isset($this->data['locations'])) {
            $this->data['locations'] = [];
        }
        /** @var list<LocationArray> $this->data['locations'] */
        $this->data['locations'][] = $value;
        return $this;
    }

    /**
     * Set multiple location tags
     *
     * @param list<LocationArray> $values
     * @return $this
     */
    public function setPodcastIndexLocations(array $values = []): self
    {
        $this->data['locations'] = [];
        foreach ($values as $value) {
            $this->addPodcastIndexLocation($value);
        }
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
     * @param PersonArray $value
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
     * @param list<PersonArray> $values
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
     * @param list<PersonArray> $values
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
     * @param list<TxtArray> $values
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
     * @param list<SocialInteractArray> $values
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
     * All value entries will be removed, including their nested valueRecipients and valueTimeSplits.
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
     * Adds a value element with one or more valueRecipients as children.
     * Optionally, a set of value time splits can also be attached.
     *
     * @param ValueArray $value
     * @param list<ValueRecipientArray> $valueRecipients
     * @param list<ValueTimeSplitArray> $valueTimeSplits
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addPodcastIndexValue(array $value, array $valueRecipients, array $valueTimeSplits = []): self
    {
        // validate the value attributes
        Validator::validateValue($value);

        // validate the valueRecipients array
        if (count($valueRecipients) < 1) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: the second argument of "value" must be an array containing
                 at least one valueRecipient'
            );
        }
        foreach ($valueRecipients as $valueRecipient) {
            Validator::validateValueRecipient($valueRecipient);
        }
        $value['valueRecipients'] = $valueRecipients;

        // validate and add valueTimeSplits
        if ($valueTimeSplits && count($valueTimeSplits) > 0) {
            foreach ($valueTimeSplits as $split) {
                Validator::validateValueTimeSplit($split);
            }
            $value['valueTimeSplits'] = $valueTimeSplits;
        }

        // add the values entry
        if (! isset($this->data['values'])) {
            $this->data['values'] = [];
        }

        /** @var list<ValueArray> $this->data['values'] */
        $this->data['values'][] = $value;

        return $this;
    }

    /**
     * Set entry season
     *
     * @param SeasonArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexSeason(array $value): self
    {
        Validator::validateSeason($value);
        $this->data['season'] = $value;
        return $this;
    }

    /**
     * Set entry episode
     *
     * @param EpisodeArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexEpisode(array $value): self
    {
        Validator::validateEpisode($value);
        $this->data['episode'] = $value;
        return $this;
    }

    /**
     * Set entry alternateEnclosure
     *
     * @param AlternateEnclosureArray $enclosure
     * @param list<SourceArray> $sources
     * @param null|IntegrityArray $integrity
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addPodcastIndexAlternateEnclosure(array $enclosure, array $sources, ?array $integrity = null): self
    {
        if (count($sources) < 1) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: the second argument to "alternateEnclosure" must be an array containing
                 at least one source entry'
            );
        }

        Validator::validateAlternateEnclosure($enclosure);

        foreach ($sources as $source) {
            Validator::validateSource($source);
        }
        $enclosure['sources'] = $sources;

        if ($integrity !== null) {
            Validator::validateIntegrity($integrity);
            $enclosure['integrity'] = $integrity;
        }

        if (! isset($this->data['alternateEnclosures'])) {
            $this->data['alternateEnclosures'] = [];
        }

        /** @var list<AlternateEnclosureArray> $this->data['alternateEnclosures'] */
        $this->data['alternateEnclosures'][] = $enclosure;
        return $this;
    }

    /**
     * Reset all alternate enclosure elements.
     * All alternate enclosure entries will be removed, including their nested sources and integrities.
     *
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function resetPodcastIndexAlternateEnclosures(): self
    {
        $this->data['alternateEnclosures'] = [];
        return $this;
    }

    /**
     * Adds an `image` element to the episode.
     *
     * @param DetailedImageArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
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
     * Sets multiple episode `image` elements.
     * If no argument is passed, all existing image entries are removed.
     *
     * @param list<DetailedImageArray> $values
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
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
     * Adds an `contentLink` element to the episode.
     *
     * @param ContentLinkArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addPodcastIndexContentLink(array $value): self
    {
        Validator::validateContentLink($value);

        if (! isset($this->data['contentLinks'])) {
            $this->data['contentLinks'] = [];
        }

        /** @var list<ContentLinkArray> $this->data['contentLinks'] */
        $this->data['contentLinks'][] = $value;
        return $this;
    }

    /**
     * Sets multiple episode `contentLink` elements.
     * If no argument is passed, all existing entries are removed.
     *
     * @param list<ContentLinkArray> $values
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function setPodcastIndexContentLinks(array $values = []): self
    {
        $this->data['contentLinks'] = [];
        foreach ($values as $value) {
            $this->addPodcastIndexContentLink($value);
        }
        return $this;
    }

    /**
     * Adds a funding tag.
     *
     * @param FundingArray $value
     * @return $this
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function addPodcastIndexFunding(array $value): self
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
     * @throws Writer\Exception\InvalidArgumentException
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
