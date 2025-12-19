<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex\Renderer;

use DateTime;
use DateTimeInterface;
use DOMDocument;
use DOMElement;

use function gettype;
use function is_string;
use function number_format;

/**
 * Creates PodcastIndex elements for feed and entry renderer.
 * This class is internal to the library and should not be referenced by consumer code.
 * Backwards Incompatible changes can occur in Minor and Patch Releases.
 *
 * @internal
 *
 * @psalm-internal Laminas\Feed
 * @psalm-internal LaminasTest\Feed
 */
final class ElementGenerator
{
    /**
     * Create PodcastIndex element
     *
     * @psalm-param DOMDocument $dom
     * @psalm-param array $data
     * @psalm-param string $name
     * @psalm-param string $nodeValue
     */
    public static function createPodcastIndexElement(
        DOMDocument $dom,
        array $data,
        string $name,
        string $nodeValue = ''
    ): DOMElement {
        $tagName = 'podcast:' . $name;
        $element = $dom->createElement($tagName);

        /**
         * @psalm-var string $key
         * @psalm-var mixed $value
         */
        foreach ($data as $key => $value) {
            if ($key === $nodeValue) {
                if (! is_string($value)) {
                    $value = (string) $value;
                }
                $text = $dom->createTextNode($value);
                $element->appendChild($text);
                continue;
            }
            if ($key === "aspectRatio") {
                $key = "aspect-ratio";
            }
            switch (gettype($value)) {
                case 'string':
                    if ($value !== '') {
                        $element->setAttribute($key, $value);
                    }
                    break;
                case 'integer':
                    $element->setAttribute($key, (string) $value);
                    break;
                case 'double':
                    // ensure decimal number instead of scientific notation, and remove thousands comma seperator
                    if ($name === 'value' && $key === 'suggested') {
                        $num = number_format($value, 11, '.', '');
                    } else {
                        $num = number_format($value, 2, '.', '');
                    }
                    $element->setAttribute($key, $num);
                    break;
                case 'boolean':
                    $bool = $value ? 'true' : 'false';
                    $element->setAttribute($key, $bool);
                    break;
                case 'object':
                    if ($value instanceof DateTime) {
                        $date = $value->format(DateTimeInterface::ATOM);
                        $element->setAttribute($key, $date);
                    }
                    break;
                default:
                    break;
            }
        }
        return $element;
    }
}
