<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex;

use DateTimeInterface;
use Laminas\Feed\Writer;

use function array_flip;
use function array_intersect_key;
use function count;
use function ctype_alpha;
use function filter_var;
use function in_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_numeric;
use function is_string;
use function strlen;

use const FILTER_VALIDATE_URL;

/**
 * Validates PodcastIndex data that exists for both, Feeds and Entries.
 * This class is internal to the library and should not be referenced by consumer code.
 * Backwards Incompatible changes can occur in Minor and Patch Releases.
 *
 * @internal
 *
 * @psalm-internal Laminas\Feed
 * @psalm-internal LaminasTest\Feed
 *
 * @psalm-type LockedArray = array{
 *        value: string,
 *        owner: string
 *      }
 * @psalm-type FundingArray = array{
 *        title: string,
 *        url: string
 *      }
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
 * @psalm-type LiveItemArray = array{
 *        status: string,
 *        start: string,
 *        end?: string
 *      }
 * @psalm-type ContentLinkArray = array{
 *         href: string,
 *         description: string,
 *       }
 * @psalm-type ChatArray = array{
 *         server: string,
 *         protocol: string,
 *         accountId?: string,
 *         space?: string,
 *       }
 */
final class Validator
{
    /**
     * Validate locked
     *
     * @param array<array-key, mixed> $value
     * @return LockedArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateLocked(array $value): array
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
        if (! is_string($value['owner'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "owner" of "locked" must be of type string'
            );
        }

        /** @var LockedArray $value */
        $value = array_intersect_key($value, array_flip(['value', 'owner']));
        return $value;
    }

    /**
     * Validate transcript
     *
     * @param array<array-key, mixed> $value
     * @return TranscriptArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateTranscript(array $value): array
    {
        if (! isset($value['url']) || ! isset($value['type'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "transcript" must be an array containing keys'
                . ' "url" and "type" and optionally "language" and "rel"'
            );
        }
        /** @var TranscriptArray $value */
        $value = array_intersect_key($value, array_flip(['url', 'type', 'language', 'rel']));
        return $value;
    }

    /**
     * Validate chapters
     *
     * @param array<array-key, mixed> $value
     * @return ChaptersArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateChapters(array $value): array
    {
        if (! isset($value['url']) || ! isset($value['type'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "chapters" must be an array containing keys "url" and "type"'
            );
        }
        /** @var ChaptersArray $value */
        $value = array_intersect_key($value, array_flip(['url', 'type']));
        return $value;
    }

    /**
     * Validate person
     *
     * @param array<array-key, mixed> $value
     * @return PersonArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validatePerson(array $value): array
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
        /** @var PersonArray $value */
        $value = array_intersect_key($value, array_flip(['name', 'role', 'group', 'img', 'href']));
        return $value;
    }

    /**
     * Validate license
     *
     * @param array<array-key, mixed> $value
     * @return LicenseArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateLicense(array $value): array
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
        /** @var LicenseArray $value */
        $value = array_intersect_key($value, array_flip(['identifier', 'url']));
        return $value;
    }

    /**
     * Validate funding
     *
     * @param array<array-key, mixed> $value
     * @return FundingArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateFunding(array $value): array
    {
        if (! isset($value['title'], $value['url'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "funding" must be an array containing keys "title" and "url"'
            );
        }
        if (! is_string($value['title'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "title" of "funding" must be of type string.'
            );
        }
        if (! is_string($value['url']) || ! filter_var($value['url'], FILTER_VALIDATE_URL)) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "url" of "funding": must be a url starting with "http://" or "https://"'
            );
        }
        /** @var FundingArray $value */
        $value = array_intersect_key($value, array_flip(['title', 'url']));
        return $value;
    }

    /**
     * Validate location
     *
     * @param array<array-key, mixed> $value
     * @return LocationArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateLocation(array $value): array
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
        /** @var LocationArray $value */
        $value = array_intersect_key($value, array_flip(['description', 'geo', 'osm', 'rel', 'country']));
        return $value;
    }

    /**
     * Validates txt
     *
     * @param array<array-key, mixed> $value
     * @return TxtArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateTxt(array $value): array
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
        /** @var TxtArray $value */
        $value = array_intersect_key($value, array_flip(['value', 'purpose']));
        return $value;
    }

    /**
     * Validates images srcset
     *
     * @param array<array-key, mixed> $value
     * @return ImagesArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateImages(array $value): array
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
        /** @var ImagesArray $value */
        $value = array_intersect_key($value, array_flip(['srcset']));
        return $value;
    }

    /**
     * Validates image
     *
     * @param array<array-key, mixed> $value
     * @return DetailedImageArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateDetailedImage(array $value): array
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
        /** @var DetailedImageArray $value */
        $value = array_intersect_key($value, array_flip([
            'href',
            'alt',
            'purpose',
            'type',
            'aspectRatio',
            'width',
            'height',
        ]));
        return $value;
    }

    /**
     * Validates update frequency
     *
     * @param array<array-key, mixed> $value
     * @return UpdateFrequencyArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateUpdateFrequency(array $value): array
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
        /** @var UpdateFrequencyArray $value */
        $value = array_intersect_key($value, array_flip(['description', 'complete', 'dtstart', 'rrule']));
        return $value;
    }

    /**
     * Validates trailer
     *
     * @param array<array-key, mixed> $value
     * @return TrailerArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateTrailer(array $value): array
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
        /** @var TrailerArray $value */
        $value = array_intersect_key($value, array_flip(['title', 'pubdate', 'url', 'length', 'type', 'season']));
        return $value;
    }

    /**
     * Validates social interact
     *
     * @param array<array-key, mixed> $value
     * @return SocialInteractArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateSocialInteract(array $value): array
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
        /** @var SocialInteractArray $value */
        $value = array_intersect_key($value, array_flip(['protocol', 'uri', 'priority', 'accountId', 'accountUrl']));
        return $value;
    }

    /**
     * Validates guid
     *
     * @param array<array-key, mixed> $value
     * @return array{value: string}
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateGuid(array $value): array
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
        /** @var array{value: string} $value */
        $value = array_intersect_key($value, array_flip(['value']));
        return $value;
    }

    /**
     * Validates medium
     *
     * @param array<array-key, mixed> $value
     * @return array{value: string}
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateMedium(array $value): array
    {
        if (! isset($value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "medium" must be an array containing the key "value"'
            );
        }
        if (! is_string($value['value'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "value" of "medium" must be a UUIDv5 string'
            );
        }
        /** @var array{value: string} $value */
        $value = array_intersect_key($value, array_flip(['value']));
        return $value;
    }

    /**
     * Validates block
     *
     * @param array<array-key, mixed> $value
     * @return BlockArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateBlock(array $value): array
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
        /** @var BlockArray $value */
        $value = array_intersect_key($value, array_flip(['value', 'id']));
        return $value;
    }

    /**
     * Validates block
     *
     * @param array<array-key, mixed> $value
     * @return array{usesPodping: bool}
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validatePodping(array $value): array
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
        /** @var array{usesPodping: bool} $value */
        $value = array_intersect_key($value, array_flip(['usesPodping']));
        return $value;
    }

    /**
     * Validate the values of the remote item.
     *
     * @param array<array-key, mixed> $value
     * @return RemoteItemArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateRemoteItem(array $value): array
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
        /** @var RemoteItemArray $value */
        $value = array_intersect_key($value, array_flip(['feedGuid', 'feedUrl', 'itemGuid', 'medium', 'title']));
        return $value;
    }

    /**
     * Validates value
     *
     * @param array<array-key, mixed> $value
     * @return ValueArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateValue(array $value): array
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
        /** @var ValueArray $value */
        $value = array_intersect_key($value, array_flip([
            'type',
            'method',
            'suggested',
            'valueRecipients',
            'valueTimeSplits',
        ]));
        return $value;
    }

    /**
     * Validates valueRecipient
     *
     * @param array<array-key, mixed> $value
     * @return ValueRecipientArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateValueRecipient(array $value): array
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
        /** @var ValueRecipientArray $value */
        $value = array_intersect_key($value, array_flip([
            'type',
            'address',
            'split',
            'name',
            'customKey',
            'customValue',
            'fee',
        ]));
        return $value;
    }

    /**
     * Validate the attributes of the value time split.
     *
     * @param array<array-key, mixed> $value
     * @return ValueTimeSplitArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateValueTimeSplit(array $value): array
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
        /** @var list<array<array-key, mixed>> $valueRecipients */
        $valueRecipients = $value['valueRecipients'] ?? [];

        /** @var array<array-key, mixed> $remoteItem */
        $remoteItem = $value['remoteItem'] ?? [];

        // check that exactly one of valueRecipients or remoteItem is set
        $usesRecipients = count($valueRecipients) > 0;
        $usesRemoteItem = count($remoteItem) > 0;

        /** @var ValueTimeSplitArray $value */
        $value = array_intersect_key($value, array_flip([
            'startTime',
            'duration',
            'remoteStartTime',
            'remotePercentage',
        ]));

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
            $value['valueRecipients'] = [];
            foreach ($valueRecipients as $valueRecipient) {
                $value['valueRecipients'][] = self::validateValueRecipient($valueRecipient);
            }
        } else {
            $value['remoteItem'] = self::validateRemoteItem($remoteItem);
        }
        return $value;
    }

    /**
     * Validates alternate enclosure
     *
     * @param array<array-key, mixed> $value
     * @return AlternateEnclosureArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateAlternateEnclosure(array $value): array
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
        /** @var AlternateEnclosureArray $value */
        $value = array_intersect_key($value, array_flip([
            'type',
            'length',
            'bitrate',
            'height',
            'lang',
            'title',
            'rel',
            'codecs',
            'default',
        ]));
        return $value;
    }

    /**
     * Validates source
     *
     * @param array<array-key, mixed> $value
     * @return SourceArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateSource(array $value): array
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
        /** @var SourceArray $value */
        $value = array_intersect_key($value, array_flip(['uri', 'contentType']));
        return $value;
    }

    /**
     * Validates integrity
     *
     * @param array<array-key, mixed> $value
     * @return IntegrityArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateIntegrity(array $value): array
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
        /** @var IntegrityArray $value */
        $value = array_intersect_key($value, array_flip(['type', 'value']));
        return $value;
    }

    /**
     * Validates season
     *
     * @param array<array-key, mixed> $value
     * @return SeasonArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateSeason(array $value): array
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

        /** @var SeasonArray $value */
        $value = array_intersect_key($value, array_flip(['value', 'name']));
        return $value;
    }

    /**
     * Validates episode
     *
     * @param array<array-key, mixed> $value
     * @return EpisodeArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateEpisode(array $value): array
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
        /** @var EpisodeArray $value */
        $value = array_intersect_key($value, array_flip(['value', 'display']));
        return $value;
    }

    /**
     * Validates live item
     *
     * @param array<array-key, mixed> $value
     * @return LiveItemArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateLiveItem(array $value): array
    {
        if (! isset($value['status'], $value['start'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "liveItem" must be an array containing at least the keys "status" and "start"'
            );
        }
        if (! is_string($value['status'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "status" of "liveItem" must be of type string'
            );
        }
        if (! is_string($value['start'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "start" of "liveItem" must be of type string'
            );
        }
        if (isset($value['end']) && ! is_string($value['end'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "end" of "liveItem" must be of type string'
            );
        }

        /** @var LiveItemArray $value */
        $value = array_intersect_key($value, array_flip(['status', 'start', 'end']));
        return $value;
    }

    /**
     * Validates content link
     *
     * @param array<array-key, mixed> $value
     * @return ContentLinkArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateContentLink(array $value): array
    {
        if (! isset($value['href'], $value['description'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "contentLink" must be an array containing the keys "href" and "description"'
            );
        }
        if (! filter_var($value['href'], FILTER_VALIDATE_URL)) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "href" of "contentLink" must be a url, starting with "http://" or "https://"'
            );
        }
        if (! is_string($value['description'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "description" of "contentLink" must be of type string'
            );
        }
        /** @var ContentLinkArray $value */
        $value = array_intersect_key($value, array_flip(['href', 'description']));
        return $value;
    }

    /**
     * Validates chat
     *
     * @param array<array-key, mixed> $value
     * @return ChatArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateChat(array $value): array
    {
        if (! isset($value['server'], $value['protocol'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: "chat" must be an array containing at least the keys "server" and "protocol"'
            );
        }
        if (! is_string($value['server'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "server" of "chat" must be of type string'
            );
        }
        if (! is_string($value['protocol'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "protocol" of "chat" must be of type string'
            );
        }
        if (isset($value['accountId']) && ! is_string($value['accountId'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "accountId" of "chat" must be of type string'
            );
        }
        if (isset($value['space']) && ! is_string($value['space'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "space" of "chat" must be of type string'
            );
        }
        /** @var ChatArray $value */
        $value = array_intersect_key($value, array_flip(['server', 'protocol', 'accountId', 'space']));
        return $value;
    }

    /**
     * Validates soundbite
     *
     * @param array<array-key, mixed> $value
     * @return SoundbiteArray
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateSoundbite(array $value): array
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
        /** @var SoundbiteArray $value */
        $value = array_intersect_key($value, array_flip(['startTime', 'duration', 'title']));
        return $value;
    }
}
