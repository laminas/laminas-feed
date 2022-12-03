<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Collection;

use ArrayObject;

/**
 * @template TKey
 * @template TValue
 * @template-extends ArrayObject<TKey, TValue>
 */
abstract class AbstractCollection extends ArrayObject
{
    /**
     * Return a simple array of the most relevant slice of
     * the collection values. For example, feed categories contain
     * the category name, domain/URI, and other data. This method would
     * merely return the most useful data - i.e. the category names.
     *
     * @return array<int, string>
     */
    abstract public function getValues();
}
