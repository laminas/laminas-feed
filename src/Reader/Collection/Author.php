<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Collection;

use function array_unique;

class Author extends AbstractCollection
{
    /**
     * Return a simple array of the most relevant slice of
     * the author values, i.e. all author names.
     *
     * @return array
     */
    public function getValues()
    {
        $authors = [];
        foreach ($this->getIterator() as $element) {
            $authors[] = $element['name'];
        }
        return array_unique($authors);
    }
}
