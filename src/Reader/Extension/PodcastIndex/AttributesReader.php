<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Extension\PodcastIndex;

// phpcs:disable SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use DateTimeInterface;
// phpcs:enable SlevomatCodingStandard.Namespaces.UnusedUses.UnusedUse
use DOMElement;
use stdClass;

/**
 * Reads PodcastIndex data that exists for both, Feeds and Entries.
 * This class is internal to the library and should not be referenced by consumer code.
 * Backwards Incompatible changes can occur in Minor and Patch Releases.
 *
 * @internal
 *
 * @psalm-internal Laminas\Feed
 * @psalm-internal LaminasTest\Feed
 *
 * @psalm-type FundingObject = object{
 *         title: string,
 *         url: string
 *       }
 * @psalm-type LicenseObject = object{
 *        identifier: string,
 *        url: string
 *      }
 * @psalm-type LocationObject = object{
 *        description: string,
 *        geo: string,
 *        osm: string,
 *        rel: string,
 *        country: string,
 *      }
 * @psalm-type BlockObject = object{
 *        value: string,
 *        id: string
 *      }
 * @psalm-type TxtObject = object{
 *        value: string,
 *        purpose: string
 *      }
 * @psalm-type UpdateFrequencyObject = object{
 *        description: string,
 *        complete: bool,
 *        dtstart: DateTimeInterface,
 *        rrule: string
 *      }
 * @psalm-type PersonObject = object{
 *       name: string,
 *       role: string,
 *       group: string,
 *       img: string,
 *       href: string
 *     }
 * @psalm-type TrailerObject = object{
 *       title: string,
 *       pubdate: string,
 *       url: string,
 *       length: int,
 *       type: string,
 *       season: int
 *     }
 * @psalm-type RemoteItemObject = object{
 *       feedGuid: string,
 *       feedUrl: string,
 *       itemGuid: string,
 *       medium: string,
 *       title: string
 *     }
 * @psalm-type ValueRecipientObject = object{
 *       type: string,
 *       address: string,
 *       split: int,
 *       name: string,
 *       customKey: string,
 *       customValue: string,
 *       fee: bool,
 *     }
 * @psalm-type ValueTimeSplitObject = object{
 *        startTime: int,
 *        duration: int,
 *        remoteStartTime: int,
 *        remotePercentage: int,
 *        valueRecipients: list<ValueRecipientObject>,
 *        remoteItem: RemoteItemObject
 *      }
 * @psalm-type ValueObject = object{
 *       type: string,
 *       method: string,
 *       suggested: float,
 *       valueRecipients: list<ValueRecipientObject>,
 *       valueTimeSplits: list<ValueTimeSplitObject>,
 *     }
 * @psalm-type ImagesObject = object{
 *        srcset: string,
 *      }
 * @psalm-type DetailedImageObject = object{
 *        href: string,
 *        alt: string,
 *        purpose: string,
 *        type: string,
 *        aspectRatio: string,
 *        width: int,
 *        height: int,
 *      }
 * @psalm-type SocialInteractObject = object{
 *       protocol: string,
 *       uri: string,
 *       priority: int,
 *       accountId: string,
 *       accountUrl: string,
 *     }
 * @psalm-type TranscriptObject = object{
 *       url: string,
 *       type: string,
 *       language: string,
 *       rel: string
 *     }
 * @psalm-type ChaptersObject = object{
 *       url: string,
 *       type: string
 *     }
 * @psalm-type SoundbiteObject = object{
 *       title: string,
 *       startTime: string,
 *       duration: string
 *     }
 * @psalm-type SeasonObject = object{
 *         value: int,
 *         name: string
 *       }
 * @psalm-type EpisodeObject = object{
 *         value: int|float,
 *         display: string
 *       }
 * @psalm-type SourceObject = object{
 *        uri: string,
 *        contentType: string
 *      }
 * @psalm-type IntegrityObject = object{
 *        type: string,
 *        value: string
 *      }
 * @psalm-type AlternateEnclosureObject = object{
 *        type: string,
 *        length: int,
 *        bitrate: int|float,
 *        height: int,
 *        lang: string,
 *        title: string,
 *        rel: string,
 *        codecs: string,
 *        default: bool,
 *        sources: list<SourceObject>,
 *        integrity: IntegrityObject,
 *     }
 * @psalm-type ContentLinkObject = object{
 *        href: string,
 *        description: string,
 *     }
 * @psalm-type ChatObject = object{
 *        server: string,
 *        protocol: string,
 *        accountId: string,
 *        space: string,
 *      }
 */
final class AttributesReader
{
    /**
     * Read feed or item license
     *
     * @psalm-return LicenseObject
     */
    public static function readLicense(DOMElement $item): object
    {
        $license             = new stdClass();
        $license->identifier = $item->nodeValue;
        $license->url        = $item->getAttribute('url');

        return $license;
    }

    /**
     * Read podcast location
     *
     * @psalm-return LocationObject
     */
    public static function readLocation(DOMElement $item): object
    {
        $location              = new stdClass();
        $location->description = $item->nodeValue;
        $location->geo         = $item->getAttribute('geo');
        $location->osm         = $item->getAttribute('osm');
        $location->rel         = $item->getAttribute('rel');
        $location->country     = $item->getAttribute('country');
        return $location;
    }

    /**
     * Read podcast images
     *
     * @psalm-return ImagesObject
     */
    public static function readImages(DOMElement $item): object
    {
        $images         = new stdClass();
        $images->srcset = $item->getAttribute('srcset');
        return $images;
    }

    /**
     * Read podcast images
     *
     * @psalm-return DetailedImageObject
     */
    public static function readDetailedImage(DOMElement $item): object
    {
        $image              = new stdClass();
        $image->href        = $item->getAttribute('href');
        $image->alt         = $item->getAttribute('alt');
        $image->aspectRatio = $item->getAttribute('aspect-ratio');
        $image->width       = $item->getAttribute('width');
        $image->height      = $item->getAttribute('height');
        $image->type        = $item->getAttribute('type');
        $image->purpose     = $item->getAttribute('purpose');
        return $image;
    }

    /**
     * Read podcast update frequency
     *
     * @psalm-return UpdateFrequencyObject
     */
    public static function readUpdateFrequency(DOMElement $item): object
    {
        $updateFrequency              = new stdClass();
        $updateFrequency->description = $item->nodeValue;
        $updateFrequency->complete    = $item->getAttribute('complete');
        $updateFrequency->dtstart     = $item->getAttribute('dtstart');
        $updateFrequency->rrule       = $item->getAttribute('rrule');

        return $updateFrequency;
    }

    /**
     * Read podcast people
     *
     * @psalm-return PersonObject
     */
    public static function readPerson(DOMElement $item): object
    {
        $person        = new stdClass();
        $person->name  = $item->nodeValue;
        $person->role  = $item->getAttribute('role');
        $person->group = $item->getAttribute('group');
        $person->img   = $item->getAttribute('img');
        $person->href  = $item->getAttribute('href');

        return $person;
    }

    /**
     * Read podcast trailer
     *
     * @psalm-return TrailerObject
     */
    public static function readTrailer(DOMElement $item): object
    {
        $object          = new stdClass();
        $object->title   = $item->nodeValue;
        $object->pubdate = $item->getAttribute('pubdate');
        $object->url     = $item->getAttribute('url');
        $object->length  = $item->getAttribute('length');
        $object->type    = $item->getAttribute('type');
        $object->season  = $item->getAttribute('season');

        return $object;
    }

    /**
     * Read podcast guid
     *
     * @psalm-return object{value: string}
     */
    public static function readGuid(DOMElement $item): object
    {
        $object        = new stdClass();
        $object->value = $item->nodeValue;

        return $object;
    }

    /**
     * Read podcast medium
     *
     * @psalm-return object{value: string}
     */
    public static function readMedium(DOMElement $item): object
    {
        $object        = new stdClass();
        $object->value = $item->nodeValue;
        return $object;
    }

    /**
     * Read podcast blocks
     *
     * @psalm-return BlockObject
     */
    public static function readBlock(DOMElement $item): object
    {
        $object        = new stdClass();
        $object->value = $item->nodeValue;
        $object->id    = $item->getAttribute('id');
        return $object;
    }

    /**
     * Read podcast txts
     *
     * @psalm-return TxtObject
     */
    public static function readTxt(DOMElement $item): object
    {
        $object          = new stdClass();
        $object->value   = $item->nodeValue;
        $object->purpose = $item->getAttribute('purpose');
        return $object;
    }

    /**
     * Read podcast remote item
     *
     * @psalm-return RemoteItemObject
     */
    public static function readRemoteItem(DOMElement $item): object
    {
        $object           = new stdClass();
        $object->feedGuid = $item->getAttribute('feedGuid');
        $object->feedUrl  = $item->getAttribute('feedUrl');
        $object->itemGuid = $item->getAttribute('itemGuid');
        $object->medium   = $item->getAttribute('medium');
        $object->title    = $item->getAttribute('title');

        return $object;
    }

    /**
     * Read podcast podroll remote items
     *
     * @psalm-return ValueObject
     */
    public static function readValue(DOMElement $item): object
    {
        $valueObject            = new stdClass();
        $valueObject->type      = $item->getAttribute('type');
        $valueObject->method    = $item->getAttribute('method');
        $valueObject->suggested = $item->getAttribute('suggested');
        return $valueObject;
    }

    /**
     * Read single remote item
     *
     * @psalm-return ValueRecipientObject
     */
    public static function readValueRecipient(DOMElement $item): object
    {
        $object              = new stdClass();
        $object->name        = $item->getAttribute('name');
        $object->type        = $item->getAttribute('type');
        $object->address     = $item->getAttribute('address');
        $object->split       = $item->getAttribute('split');
        $object->customKey   = $item->getAttribute('customKey');
        $object->customValue = $item->getAttribute('customValue');
        $object->fee         = $item->getAttribute('fee');

        return $object;
    }

    /**
     * Read single value time split
     *
     * @return ValueTimeSplitObject
     */
    public static function readValueTimeSplit(DOMElement $entry): object
    {
        $object                   = new stdClass();
        $object->startTime        = $entry->getAttribute('startTime');
        $object->duration         = $entry->getAttribute('duration');
        $object->remoteStartTime  = $entry->getAttribute('remoteStartTime');
        $object->remotePercentage = $entry->getAttribute('remotePercentage');

        return $object;
    }

    /**
     * Read podcast social interacts
     *
     * @psalm-return SocialInteractObject
     */
    public static function readSocialInteract(DOMElement $item): object
    {
        $object             = new stdClass();
        $object->protocol   = $item->getAttribute('protocol');
        $object->uri        = $item->getAttribute('uri');
        $object->priority   = $item->getAttribute('priority');
        $object->accountId  = $item->getAttribute('accountId');
        $object->accountUrl = $item->getAttribute('accountUrl');
        return $object;
    }

    /**
     * Read podcast alternate enclosure
     *
     * @psalm-return AlternateEnclosureObject
     */
    public static function readAlternateEnclosure(DOMElement $item): object
    {
        $object          = new stdClass();
        $object->type    = $item->getAttribute('type');
        $object->length  = $item->getAttribute('length');
        $object->bitrate = $item->getAttribute('bitrate');
        $object->height  = $item->getAttribute('height');
        $object->lang    = $item->getAttribute('lang');
        $object->title   = $item->getAttribute('title');
        $object->rel     = $item->getAttribute('rel');
        $object->codecs  = $item->getAttribute('codecs');
        $object->default = $item->getAttribute('default');
        return $object;
    }

    /**
     * Read podcast source
     *
     * @psalm-return SourceObject
     */
    public static function readSource(DOMElement $item): object
    {
        $object              = new stdClass();
        $object->uri         = $item->getAttribute('uri');
        $object->contentType = $item->getAttribute('contentType');
        return $object;
    }

    /**
     * Read podcast integrity
     *
     * @psalm-return SourceObject
     */
    public static function readIntegrity(DOMElement $item): object
    {
        $object        = new stdClass();
        $object->type  = $item->getAttribute('type');
        $object->value = $item->getAttribute('value');
        return $object;
    }

    /**
     * Read content link
     *
     * @psalm-return ContentLinkObject
     */
    public static function readContentLink(DOMElement $item): object
    {
        $object              = new stdClass();
        $object->href        = $item->getAttribute('href');
        $object->description = $item->nodeValue;
        return $object;
    }

    /**
     * Read podcast funding
     *
     * @psalm-return FundingObject
     */
    public static function readFunding(DOMElement $item): object
    {
        $object        = new stdClass();
        $object->url   = $item->getAttribute('url');
        $object->title = $item->nodeValue;
        return $object;
    }

    /**
     * Read podcast chat
     *
     * @psalm-return ChatObject
     */
    public static function readChat(DOMElement $item): object
    {
        $object            = new stdClass();
        $object->server    = $item->getAttribute('server');
        $object->protocol  = $item->getAttribute('protocol');
        $object->accountId = $item->getAttribute('accountId');
        $object->space     = $item->getAttribute('space');
        return $object;
    }
}
