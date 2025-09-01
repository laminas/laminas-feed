<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex;

use DateTimeInterface;
use Laminas\Feed\Writer;

use function filter_var;
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
 * @psalm-type ValueArray = array{
 *      type: string,
 *      method: string,
 *      suggested?: float,
 *      recipients?: list<ValueRecipientArray>
 *    }
 * @psalm-type ImageArray = array{
 *      href: string,
 *      alt?: string,
 *      purpose?: string,
 *      type?: string,
 *      aspect-ratio?: string,
 *      width?: int,
 *      height?: int,
 *    }
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
 *      title?: string,
 *      startTime: string,
 *      duration: string
 *    }
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
     * @param ImageArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    /*public static function validateImage(array $value): void
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
        if (isset($value['aspect-ratio']) && ! is_string($value['aspect-ratio'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "aspect-ratio" of "image" must be a string. examples: "1/1", "16/9", "4/1"'
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
    }*/

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
     * Validates value recipient
     *
     * @param ValueRecipientArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public static function validateValueRecipient(array $value): void
    {
        if (! isset($value['type'], $value['address'], $value['split'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: each "recipients" entry in "value" must be an array 
                containing the keys "type", "address" and "split"'
            );
        }
        if (! is_string($value['type'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "type" of "value recipient" must be of type string'
            );
        }
        if (! is_string($value['address'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "address" of "value recipient" must be of type string'
            );
        }
        if (! is_int($value['split'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "split" of "value recipient" must be of type integer'
            );
        }
        if (isset($value['name']) && ! is_string($value['name'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "name" of "value recipient" must be of type string'
            );
        }
        if (isset($value['customKey']) && ! is_string($value['customKey'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "customKey" of "value recipient" must be of type string'
            );
        }
        if (isset($value['customValue']) && ! is_string($value['customValue'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "customKey" of "value recipient" must be of type string'
            );
        }
        if (isset($value['fee']) && ! is_bool($value['fee'])) {
            throw new Writer\Exception\InvalidArgumentException(
                'invalid parameter: key "fee" of "value recipient" must be of type boolean'
            );
        }
    }

    /**
     * TODO: Validates value time split
     */
    /*public static function validateValueTimeSplit(array $value): void
    {
        //
    }*/
}
