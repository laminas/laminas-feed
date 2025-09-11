<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex;

use DateTimeInterface;
use Laminas\Feed\Writer;

use function count;
use function filter_var;
use function in_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;

use const FILTER_VALIDATE_URL;

/**
 * Validates PodcastIndex data that exists for both, Feeds and Entries
 *
 * @psalm-type LicenseArray = array{
 *       identifier: string,
 *       url?: string
 *     }
 * @psalm-type LocationArray = array{
 *       description: string,
 *       geo?: string,
 *       osm?: string,
 *       rel?: string,
 *       country?: string,
 *     }
 * @psalm-type BlockArray = array{
 *       value: string,
 *       id?: string
 *     }
 * @psalm-type TxtArray = array{
 *       value: string,
 *       purpose?: string
 *     }
 * @psalm-type UpdateFrequencyArray = array{
 *       description: string,
 *       complete?: bool,
 *       dtstart?: DateTimeInterface,
 *       rrule?: string
 *     }
 * @psalm-type PersonArray = array{
 *      name: string,
 *      role?: string,
 *      group?: string,
 *      img?: string,
 *      href?: string
 *    }
 * @psalm-type TrailerArray = array{
 *      title: string,
 *      pubdate: string,
 *      url: string,
 *      length?: int,
 *      type?: string,
 *      season?: int
 *    }
 * @psalm-type RemoteItemArray = array{
 *      feedGuid: string,
 *      feedUrl?: string,
 *      itemGuid?: string,
 *      medium?: string,
 *      title?: string
 *    }
 * @psalm-type ValueRecipientArray = array{
 *      type: string,
 *      address: string,
 *      split: int,
 *      name?: string,
 *      customKey?: string,
 *      customValue?: string,
 *      fee?: bool,
 *    }
 * @psalm-type ValueTimeSplitArray = array{
 *       startTime: int,
 *       duration: int,
 *       remoteStartTime?: int,
 *       remotePercentage?: int,
 *       valueRecipients?: list<ValueRecipientArray>,
 *       remoteItem?: RemoteItemArray
 *     }
 * @psalm-type ValueArray = array{
 *      type: string,
 *      method: string,
 *      suggested?: float,
 *      valueRecipients?: list<ValueRecipientArray>,
 *      valueTimeSplits?: list<ValueTimeSplitArray>
 *    }
 * @psalm-type ImagesArray = array{
 *       srcset: string,
 *     }
 * @psalm-type DetailedImageArray = array{
 *       href: string,
 *       alt?: string,
 *       purpose?: string,
 *       type?: string,
 *       aspectRatio?: string,
 *       width?: int,
 *       height?: int,
 *     }
 * @psalm-type SocialInteractArray = array{
 *      protocol: string,
 *      uri: string,
 *      priority?: int,
 *      accountId?: string,
 *      accountUrl?: string,
 *    }
 * @psalm-type TranscriptArray = array{
 *      url: string,
 *      type: string,
 *      language?: string,
 *      rel?: string
 *    }
 * @psalm-type ChaptersArray = array{
 *      url: string,
 *      type: string
 *    }
 * @psalm-type SoundbiteArray = array{
 *       title?: string,
 *       startTime: string,
 *       duration: string
 *     }
 * @psalm-type SeasonArray = array{
 *        value: int,
 *        name?: string
 *      }
 * @psalm-type EpisodeArray = array{
 *        value: int|float,
 *        display?: string
 *      }
 * @psalm-type SourceArray = array{
 *       uri: string,
 *       contentType?: string
 *     }
 * @psalm-type IntegrityArray = array{
 *       type: string,
 *       value: string
 *     }
 * @psalm-type AlternateEnclosureArray = array{
 *       type: string,
 *       length?: int,
 *       bitrate?: int|float,
 *       height?: int,
 *       lang?: string,
 *       title?: string,
 *       rel?: string,
 *       codecs?: string,
 *       default?: bool,
 *       sources?: list<SourceArray>,
 *       integrity?: IntegrityArray,
 *     }
 */
class Validator
{
    /**
     * Validate person
     *
     * @psalm-param PersonArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validatePerson(array $value): void
    {
        if (! isset($value['name'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "person" must be an array containing at least the key "name"'
            );
        }
        if (! is_string($value['name'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "name" of "person" must be of type string'
            );
        }
        if (isset($value['role']) && ! is_string($value['role'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "role" of "person" must be of type string'
            );
        }
        if (isset($value['group']) && ! is_string($value['group'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "group" of "person" must be of type string'
            );
        }
        if (isset($value['img']) && ! filter_var($value['img'], FILTER_VALIDATE_URL)) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "img" of "person" must be a url, starting with "http://" or "https://"'
            );
        }
        if (isset($value['href']) && ! filter_var($value['href'], FILTER_VALIDATE_URL)) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "href" of "person" must be a url, starting with "http://" or "https://"'
            );
        }
    }

    /**
     * Validate license
     *
     * @param LicenseArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateLicense(array $value): void
    {
        if (! isset($value['identifier'], $value['url'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "license" must be an array containing the keys "identifier" (node value) and "url"'
            );
        }
        if (! is_string($value['identifier'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "identifier" of "license" must be of type string.'
            );
        }
        if (! is_string($value['url']) || ! filter_var($value['url'], FILTER_VALIDATE_URL)) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "url" of "license": must be a url starting with "http://" or "https://"'
            );
        }
    }

    /**
     * Validate location
     *
     * @param LocationArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateLocation(array $value): void
    {
        if (! isset($value['description'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "location" must be an array containing at least the key "description" (node value)'
            );
        }
        if (! is_string($value['description'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "description" of "location" must be of type string.'
            );
        }
        if (isset($value['geo']) && ! is_string($value['geo'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "geo" of "location" must be of type string. example: "geo:-27.86159,153.3169"'
            );
        }
        if (isset($value['osm']) && ! is_string($value['osm'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "osm" of "location" must be of type string. example: "W43678282"'
            );
        }
        if (isset($value['rel']) && ! is_string($value['rel'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "rel" of "location" must be of type string. example: "subject"'
            );
        }
        if (isset($value['country']) && ! is_string($value['country'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "country" of "location" must be of type string. example: "US"'
            );
        }
    }

    /**
     * Validates txt
     *
     * @param TxtArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateTxt(array $value): void
    {
        if (! isset($value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "txt" must be an array containing the key "value"'
            );
        }
        if (! is_string($value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "value" of "txt" must be of type string'
            );
        }
        if (isset($value['purpose']) && ! is_string($value['purpose'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "purpose" of "txt" must be of type string'
            );
        }
    }

    /**
     * Validates images srcset
     *
     * @param array{srcset: string} $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateImages(array $value): void
    {
        if (! isset($value['srcset'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "images" must be an array containing the key "srcset"'
            );
        }
        if (! is_string($value['srcset'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "srcset" of "images" must be of type string containing comma-seperated urls'
            );
        }
    }

    /**
     * Validates image
     *
     * @param DetailedImageArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateDetailedImage(array $value): void
    {
        if (! isset($value['href'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "image" must be an array containing at least the key "href"'
            );
        }
        if (! filter_var($value['href'], FILTER_VALIDATE_URL)) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "href" of "image" must be must be a url starting with "http://" or "https://"'
            );
        }
        if (isset($value['alt']) && ! is_string($value['alt'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "alt" of "image" must be of type string'
            );
        }
        if (isset($value['aspectRatio']) && ! is_string($value['aspectRatio'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "aspectRatio" of "image" must be a string. examples: "1/1", "16/9", "4/1"'
            );
        }
        if (isset($value['width']) && ! is_int($value['width'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "width" of "image" must be of type integer'
            );
        }
        if (isset($value['height']) && ! is_int($value['height'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "height" of "image" must be of type integer'
            );
        }
        if (isset($value['type']) && ! is_string($value['type'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "type" of "image" must be of type string'
            );
        }
        if (isset($value['purpose']) && ! is_string($value['purpose'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "purpose" of "image" must be of type string'
            );
        }
    }

    /**
     * Validates update frequency
     *
     * @param UpdateFrequencyArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateUpdateFrequency(array $value): void
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
    }

    /**
     * Validates trailer
     *
     * @param TrailerArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateTrailer(array $value): void
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
    }

    /**
     * Validates social interact
     *
     * @param SocialInteractArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateSocialInteract(array $value): void
    {
        if (! isset($value['protocol'], $value['uri'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "socialInteract" must be an array containing at least the keys "protocol" and "uri"'
            );
        }
        if (! is_string($value['protocol'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "protocol" of "socialInteract" must be of type string'
            );
        }
        if (! filter_var($value['uri'], FILTER_VALIDATE_URL)) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "uri" of "socialInteract" must be a url starting with "http://" or "https://"'
            );
        }
        if (isset($value['priority']) && ! is_int($value['priority'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "priority" of "socialInteract" must be of type integer'
            );
        }
        if (isset($value['accountId']) && ! is_string($value['accountId'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "accountId" of "socialInteract" must be of type string'
            );
        }
        if (isset($value['accountUrl']) && ! filter_var($value['accountUrl'], FILTER_VALIDATE_URL)) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "accountUrl" of "socialInteract" must be a url 
                starting with "http://" or "https://"'
            );
        }
    }

    /**
     * Validates guid
     *
     * @param array{value: string} $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateGuid(array $value): void
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
    }

    /**
     * Validates medium
     *
     * @param array{value: string} $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateMedium(array $value): void
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
    }

    /**
     * Validates block
     *
     * @param BlockArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateBlock(array $value): void
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
    }

    /**
     * Validates block
     *
     * @param array{usesPodping: bool} $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validatePodping(array $value): void
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
    }

    /**
     * Validate the values of the remote item.
     *
     * @param RemoteItemArray $value
     * @throws Writer\Exception\InvalidArgumentException
     * @psalm-suppress DocblockTypeContradiction
     */
    public static function validateRemoteItem(array $value): void
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
     * Validates value
     *
     * @param ValueArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateValue(array $value): void
    {
        if (! isset($value['type'], $value['method'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: the first argument of "value" must an array 
                containing at least the keys "type" and "method"'
            );
        }
        if (! is_string($value['type'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "type" of "value" must be of type string'
            );
        }
        if (! is_string($value['method'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "method" of "value" must be of type string'
            );
        }
        if (isset($value['suggested']) && ! is_float($value['suggested'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "suggested" of "value" must be of type float'
            );
        }
    }

    /**
     * Validates valueRecipient
     *
     * @param ValueRecipientArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateValueRecipient(array $value): void
    {
        if (! isset($value['type'], $value['address'], $value['split'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: each "valueRecipients" entry in "value" must be an array 
                containing the keys "type", "address" and "split"'
            );
        }
        if (! is_string($value['type'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "type" of "valueRecipient" must be of type string'
            );
        }
        if (! is_string($value['address'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "address" of "valueRecipient" must be of type string'
            );
        }
        if (! is_int($value['split'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "split" of "valueRecipient" must be of type integer'
            );
        }
        if (isset($value['name']) && ! is_string($value['name'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "name" of "valueRecipient" must be of type string'
            );
        }
        if (isset($value['customKey']) && ! is_string($value['customKey'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "customKey" of "valueRecipient" must be of type string'
            );
        }
        if (isset($value['customValue']) && ! is_string($value['customValue'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "customKey" of "valueRecipient" must be of type string'
            );
        }
        if (isset($value['fee']) && ! is_bool($value['fee'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "fee" of "valueRecipient" must be of type boolean'
            );
        }
    }

    /**
     * Validate the attributes of the value time split.
     *
     * @psalm-param ValueTimeSplitArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateValueTimeSplit(array $value): void
    {
        if (! isset($value['startTime'], $value['duration'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "valueTimeSplit" must contain the keys "startTime" and "duration"'
            );
        }
        if (! is_int($value['startTime'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "startTime" of "valueTimeSplit" must be of type integer'
            );
        }
        if (! is_int($value['duration'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "duration" of "valueTimeSplit" must be of type integer'
            );
        }
        if (isset($value['remoteStartTime']) && ! is_int($value['remoteStartTime'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "remoteStartTime" of "valueTimeSplit" must be of type integer'
            );
        }
        if (isset($value['remotePercentage']) && ! is_int($value['remotePercentage'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "remotePercentage" of "valueTimeSplit" must be of type integer'
            );
        }

        // check that exactly one of valueRecipients or remoteItem is set
        $usesRecipients = isset($value['valueRecipients']) && count($value['valueRecipients']) > 0;
        $usesRemoteItem = isset($value['remoteItem']) && count($value['remoteItem']) > 0;

        if (! $usesRecipients && ! $usesRemoteItem) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "valueTimeSplit" must contain either "valueRecipients" or "remoteItem"'
            );
        }
        if ($usesRecipients && $usesRemoteItem) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "valueTimeSplit" must not contain both "valueRecipients" and "remoteItem"'
            );
        }
        if ($usesRecipients) {
            foreach ($value['valueRecipients'] as $valueRecipient) {
                self::validateValueRecipient($valueRecipient);
            }
        } else {
            self::validateRemoteItem($value['remoteItem']);
        }
    }

    /**
     * Validates alternate enclosure
     *
     * @param AlternateEnclosureArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateAlternateEnclosure(array $value): void
    {
        if (! isset($value['type'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "alternateEnclosure" must be an array containing at least the key "type"'
            );
        }
        if (! is_string($value['type'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "type" of "alternateEnclosure" must be of type string'
            );
        }
        if (isset($value['length']) && ! is_int($value['length'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "length" of "alternateEnclosure" must be of type integer'
            );
        }
        if (isset($value['bitrate']) && ! (is_int($value['bitrate']) || is_float($value['bitrate']))) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "bitrate" of "alternateEnclosure" must be of type integer or type float'
            );
        }
        if (isset($value['height']) && ! is_int($value['height'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "height" of "alternateEnclosure" must be of type integer'
            );
        }
        if (isset($value['lang']) && ! is_string($value['lang'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "lang" of "alternateEnclosure" must be of type string'
            );
        }
        if (isset($value['title']) && ! is_string($value['title'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "title" of "alternateEnclosure" must be of type string'
            );
        }
        if (isset($value['rel']) && ! is_string($value['rel'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "rel" of "alternateEnclosure" must be of type string'
            );
        }
        if (isset($value['codecs']) && ! is_string($value['codecs'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "codecs" of "alternateEnclosure" must be of type string'
            );
        }
        if (isset($value['default']) && ! is_bool($value['default'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "default" of "alternateEnclosure" must be of type boolean'
            );
        }
    }

    /**
     * Validates source
     *
     * @param SourceArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateSource(array $value): void
    {
        if (! isset($value['uri'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "source" must be an array containing at least the key "uri"'
            );
        }
        if (! is_string($value['uri'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "uri" of "source" must be of type string'
            );
        }
        if (isset($value['contentType']) && ! is_string($value['contentType'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "contentType" of "source" must be of type string'
            );
        }
    }

    /**
     * Validates integrity
     *
     * @param IntegrityArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateIntegrity(array $value): void
    {
        if (! isset($value['type'], $value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "integrity" must be an array containing the keys "type" and "value"'
            );
        }
        if (! is_string($value['type'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "type" of "integrity" must be of type string'
            );
        }
        if (! is_string($value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "value" of "integrity" must be of type string'
            );
        }
    }

    /**
     * Validates season
     *
     * @param SeasonArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateSeason(array $value): void
    {
        if (! isset($value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "season" must be an array containing at least the key "value"'
            );
        }
        if (! is_int($value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "value" of "season" must be of type integer'
            );
        }
        if (isset($value['name']) && ! is_string($value['name'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "name" of "season" must be of type string'
            );
        }
    }

    /**
     * Validates episode
     *
     * @param EpisodeArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateEpisode(array $value): void
    {
        if (! isset($value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "episode" must be an array containing at least the key "value"'
            );
        }
        if (! (is_int($value['value']) || is_float($value['value']))) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "value" of "episode" must be of type integer or type float'
            );
        }
        if (isset($value['display']) && ! is_string($value['display'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "display" of "episode" must be of type string'
            );
        }
    }
}
