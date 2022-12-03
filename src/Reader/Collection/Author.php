<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Collection;

use function array_unique;

/** @template-extends AbstractCollection<int, array{name: string, ...}> */
class Author extends AbstractCollection
{
    /**
     * @inheritDoc
     *
     * Return a simple array of the most relevant slice of
     * the author values, i.e. all author names.
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
