<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader;

use ArrayObject;

/**
 * @deprecated This class is deprecated. Use the concrete collection classes
 *     \Laminas\Feed\Reader\Collection\Author and \Laminas\Feed\Reader\Collection\Category
 *     or the generic class \Laminas\Feed\Reader\Collection\Collection instead.
 *
 * @template TKey of array-key
 * @template TValue
 * @template-extends ArrayObject<TKey, TValue>
 */
class Collection extends ArrayObject
{
}
