<?php

namespace Laminas\Feed\Reader\Collection;

use ArrayObject;

abstract class AbstractCollection extends ArrayObject
{
    /**
     * Return a simple array of the most relevant slice of
     * the collection values. For example, feed categories contain
     * the category name, domain/URI, and other data. This method would
     * merely return the most useful data - i.e. the category names.
     *
     * @return array
     */
    abstract public function getValues();
}
