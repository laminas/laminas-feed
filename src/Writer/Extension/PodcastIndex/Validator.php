<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex;

use DateTimeInterface;
use Laminas\Feed\Writer;

use function filter_var;
use function is_string;

use const FILTER_VALIDATE_URL;

/**
 * Validates PodcastIndex data that exists for both, Feeds and Entries
 *
 * @psalm-type UpdateFrequencyArray = array{
 *      description: string,
 *      complete?: bool,
 *      dtstart?: DateTimeInterface,
 *      rrule?: string
 *    }
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
 *        type: string,
 *        address: string,
 *        split: int,
 *        name?: string,
 *        customKey?: string,
 *        customValue?: string,
 *        fee?: bool,
 *      }
 * @psalm-type ValueArray = array{
 *      type: string,
 *      method: string,
 *      suggested?: float,
 *      recipients?: list<ValueRecipientArray>
 *    }
 */
class Validator
{
    /**
     * @param PersonArray $value
     * @throws Writer\Exception\InvalidArgumentException
     */
    public function validatePodcastIndexPerson(array $value): void
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

    public function validatePodcastIndexLicense(array $value): void
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

    public function validatePodcastIndexLocation(array $value): void
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
    }

    // Images
    public function validatePodcastIndexImages(array $value): void
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

    // Image
    public function validatePodcastIndexImage(array $value): void
    {

    }

    // Social Interact
    public function validatePodcastIndexSocialInteract(array $value): void
    {

    }

    // Txt
    public function validatePodcastIndexTxt(array $value): void
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

    // Value
    public function validatePodcastIndexValue(array $value): void
    {

    }

    // Value Recipient
    public function validatePodcastIndexValueRecipient(array $value): void
    {

    }

    // Value Time Split
    public function validatePodcastIndexValueTimeSplit(array $value): void
    {

    }
}
