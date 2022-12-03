<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Collection;

use function array_unique;

/** @template-extends AbstractCollection<int, array{term: string, scheme: string, label: string}> */
class Category extends AbstractCollection
{
    /**
     * @inheritDoc
     *
     * Return a simple array of the most relevant slice of
     * the collection values. For example, feed categories contain
     * the category name, domain/URI, and other data. This method would
     * merely return the most useful data - i.e. the category names.
     */
    public function getValues()
    {
        $categories = [];
        foreach ($this->getIterator() as $element) {
            if (isset($element['label']) && ! empty($element['label'])) {
                $categories[] = $element['label'];
            } else {
                $categories[] = $element['term'];
            }
        }
        return array_unique($categories);
    }
}
