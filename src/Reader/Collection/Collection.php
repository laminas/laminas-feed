<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Collection;

use ArrayObject;

/**
 * @template TKey of array-key
 * @template TValue
 * @template-extends ArrayObject<TKey, TValue>
 */
class Collection extends ArrayObject
{
}
