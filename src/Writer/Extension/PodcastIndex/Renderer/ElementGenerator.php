<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex\Renderer;

use DateTimeInterface;
use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension\PodcastIndex\Validator;

use function number_format;

/**
 * Creates PodcastIndex elements for feed renderer
 *
 * @psalm-import-type LicenseArray from Validator
 * @psalm-import-type LocationArray from Validator
 * @psalm-import-type BlockArray from Validator
 * @psalm-import-type TxtArray from Validator
 * @psalm-import-type PersonArray from Validator
 * @psalm-import-type UpdateFrequencyArray from Validator
 * @psalm-import-type TrailerArray from Validator
 * @psalm-import-type RemoteItemArray from Validator
 * @psalm-import-type ValueRecipientArray from Validator
 * @psalm-import-type ValueTimeSplitArray from Validator
 * @psalm-import-type ValueArray from Validator
 * @psalm-import-type ImageArray from Validator
 * @psalm-import-type SocialInteractArray from Validator
 */
class ElementGenerator
{
    /**
     * Create license element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param LicenseArray $license
     */
    public static function createLicenseElement(DOMDocument $dom, array $license): DOMElement
    {
        $el   = $dom->createElement('podcast:license');
        $text = $dom->createTextNode($license['identifier']);
        $el->appendChild($text);
        if (isset($license['url']) && $license['url'] !== '') {
            $el->setAttribute('url', $license['url']);
        }
        return $el;
    }

    /**
     * Create location element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param LocationArray $location
     */
    public static function createLocationElement(DOMDocument $dom, array $location): DOMElement
    {
        $el   = $dom->createElement('podcast:location');
        $text = $dom->createTextNode($location['description']);
        $el->appendChild($text);
        if (isset($location['geo']) && $location['geo'] !== '') {
            $el->setAttribute('geo', $location['geo']);
        }
        if (isset($location['osm']) && $location['osm'] !== '') {
            $el->setAttribute('osm', $location['osm']);
        }
        if (isset($location['rel']) && $location['rel'] !== '') {
            $el->setAttribute('rel', $location['rel']);
        }
        if (isset($location['country']) && $location['country'] !== '') {
            $el->setAttribute('country', $location['country']);
        }
        return $el;
    }

    /**
     * Create person element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param PersonArray $person
     */
    public static function createPersonElement(DOMDocument $dom, array $person): DOMElement
    {
        $el   = $dom->createElement('podcast:person');
        $text = $dom->createTextNode($person['name']);
        $el->appendChild($text);

        if (isset($person['role']) && $person['role'] !== '') {
            $el->setAttribute('role', $person['role']);
        }
        if (isset($person['group']) && $person['group'] !== '') {
            $el->setAttribute('group', $person['group']);
        }
        if (isset($person['img']) && $person['img'] !== '') {
            $el->setAttribute('img', $person['img']);
        }
        if (isset($person['href']) && $person['href'] !== '') {
            $el->setAttribute('href', $person['href']);
        }
        return $el;
    }

    /**
     * Create update frequency element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param UpdateFrequencyArray $updateFrequency
     */
    public static function createUpdateFrequencyElement(DOMDocument $dom, array $updateFrequency): DOMElement
    {
        $el   = $dom->createElement('podcast:updateFrequency');
        $text = $dom->createTextNode($updateFrequency['description']);
        $el->appendChild($text);
        if (($updateFrequency['complete'] ?? null) === true) {
            $el->setAttribute('complete', 'true');
        }
        if (isset($updateFrequency['dtstart'])) {
            $el->setAttribute('dtstart', $updateFrequency['dtstart']->format(DateTimeInterface::ATOM));
        }
        if (isset($updateFrequency['rrule']) && $updateFrequency['rrule'] !== '') {
            $el->setAttribute('rrule', $updateFrequency['rrule']);
        }
        return $el;
    }

    /**
     * Create trailer element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param TrailerArray $trailer
     */
    public static function createTrailerElement(DOMDocument $dom, array $trailer): DOMElement
    {
        $el   = $dom->createElement('podcast:trailer');
        $text = $dom->createTextNode($trailer['title']);
        $el->appendChild($text);
        $el->setAttribute('pubdate', $trailer['pubdate']);
        $el->setAttribute('url', $trailer['url']);
        if (isset($trailer['length'])) {
            $el->setAttribute('length', (string) $trailer['length']);
        }
        if (isset($trailer['type'])) {
            $el->setAttribute('type', $trailer['type']);
        }
        if (isset($trailer['season'])) {
            $el->setAttribute('season', (string) $trailer['season']);
        }
        return $el;
    }

    /**
     * Create block element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param BlockArray $block
     */
    public static function createBlockElement(DOMDocument $dom, array $block): DOMElement
    {
        $el   = $dom->createElement('podcast:block');
        $text = $dom->createTextNode($block['value']);
        $el->appendChild($text);
        if (isset($block['id']) && $block['id'] !== '') {
            $el->setAttribute('id', $block['id']);
        }
        return $el;
    }

    /**
     * Create txt element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param TxtArray $txt
     */
    public static function createTxtElement(DOMDocument $dom, array $txt): DOMElement
    {
        $el   = $dom->createElement('podcast:txt');
        $text = $dom->createTextNode($txt['value']);
        $el->appendChild($text);
        if (isset($txt['purpose']) && $txt['purpose'] !== '') {
            $el->setAttribute('purpose', $txt['purpose']);
        }
        return $el;
    }

    /**
     * TODO: Create images element
     */

    /**
     * TODO: Create image element
     */

    /**
     * Create social interact element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param SocialInteractArray $socialInteract
     */
    public static function createSocialInteractElement($dom, $socialInteract): DOMElement
    {
        $el = $dom->createElement('podcast:socialInteract');
        $el->setAttribute('protocol', $socialInteract['protocol']);
        $el->setAttribute('uri', $socialInteract['uri']);

        if (isset($socialInteract['priority'])) {
            $el->setAttribute('priority', (string) $socialInteract['priority']);
        }
        if (isset($socialInteract['accountId']) && $socialInteract['accountId'] !== '') {
            $el->setAttribute('accountId', $socialInteract['accountId']);
        }
        if (isset($socialInteract['accountUrl']) && $socialInteract['accountUrl'] !== '') {
            $el->setAttribute('accountUrl', $socialInteract['accountUrl']);
        }

        return $el;
    }

    /**
     * Create remote item element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param RemoteItemArray $remoteItem
     */
    public static function createRemoteItemElement($dom, $remoteItem): DOMElement
    {
        $el = $dom->createElement('podcast:remoteItem');
        $el->setAttribute('feedGuid', $remoteItem['feedGuid']);

        if (isset($remoteItem['feedUrl']) && $remoteItem['feedUrl'] !== '') {
            $el->setAttribute('feedUrl', $remoteItem['feedUrl']);
        }
        if (isset($remoteItem['itemGuid']) && $remoteItem['itemGuid'] !== '') {
            $el->setAttribute('itemGuid', $remoteItem['itemGuid']);
        }
        if (isset($remoteItem['medium']) && $remoteItem['medium'] !== '') {
            $el->setAttribute('medium', $remoteItem['medium']);
        }
        if (isset($remoteItem['title']) && $remoteItem['title'] !== '') {
            $el->setAttribute('title', $remoteItem['title']);
        }

        return $el;
    }

    /**
     * Create value element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param ValueArray $value
     */
    public static function createValueElement(DOMDocument $dom, array $value): DOMElement
    {
        $valueElement = $dom->createElement('podcast:value');
        $valueElement->setAttribute('type', $value['type']);
        $valueElement->setAttribute('method', $value['method']);
        if (isset($value['suggested'])) {
            // ensure float instead of scientific notation
            $suggested = number_format($value['suggested'], 11);
            $valueElement->setAttribute('suggested', $suggested);
        }
        return $valueElement;
    }

    /**
     * Create valueRecipient element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param ValueRecipientArray $valueRecipient
     */
    public static function createValueRecipientElement($dom, $valueRecipient): DOMElement
    {
        $el = $dom->createElement('podcast:valueRecipient');
        if (isset($valueRecipient['name']) && $valueRecipient['name'] !== '') {
            $el->setAttribute('name', $valueRecipient['name']);
        }
        $el->setAttribute('type', $valueRecipient['type']);
        $el->setAttribute('address', $valueRecipient['address']);
        $el->setAttribute('split', (string) $valueRecipient['split']);

        if (isset($valueRecipient['customKey']) && $valueRecipient['customKey'] !== '') {
            $el->setAttribute('customKey', $valueRecipient['customKey']);
        }
        if (isset($valueRecipient['customValue']) && $valueRecipient['customValue'] !== '') {
            $el->setAttribute('customValue', $valueRecipient['customValue']);
        }
        if (isset($valueRecipient['fee'])) {
            $fee = $valueRecipient['fee'] ? 'true' : 'false';
            $el->setAttribute('fee', $fee);
        }

        return $el;
    }

    /**
     * Create value time split element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param ValueTimeSplitArray $valueTimeSplit
     */
    public static function createValueTimeSplitElement($dom, $valueTimeSplit): DOMElement
    {
        $el = $dom->createElement('podcast:valueTimeSplit');
        $el->setAttribute('startTime', (string) $valueTimeSplit['startTime']);
        $el->setAttribute('duration', (string) $valueTimeSplit['duration']);

        if (isset($valueTimeSplit['remoteStartTime'])) {
            $el->setAttribute('remoteStartTime', (string) $valueTimeSplit['remoteStartTime']);
        }
        if (isset($valueTimeSplit['remotePercentage'])) {
            $el->setAttribute('remotePercentage', (string) $valueTimeSplit['remotePercentage']);
        }

        // set 1-n child nodes: valueRecipients
        if (isset($valueTimeSplit['valueRecipients'])) {
            foreach ($valueTimeSplit['valueRecipients'] as $valueRecipient) {
                $element = self::createValueRecipientElement($dom, $valueRecipient);
                $el->appendChild($element);
            }
        }

        // set 1 child node: value remote item
        if (isset($valueTimeSplit['remoteItem'])) {
            $element = self::createRemoteItemElement($dom, $valueTimeSplit['remoteItem']);
            $el->appendChild($element);
        }

        return $el;
    }
}
