<?php

declare(strict_types=1);

namespace My\Extension\JungleBooks;

use Laminas\Feed\Reader\Extension;

use function is_string;

class Entry extends Extension\AbstractEntry
{
    /** @return null|string */
    public function getIsbn()
    {
        if (isset($this->data['isbn']) && is_string($this->data['isbn'])) {
            return $this->data['isbn'];
        }

        $isbn = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/jungle:isbn)');
        if (! is_string($isbn)) {
            $isbn = null;
        }

        $this->data['isbn'] = $isbn;
        return $this->data['isbn'];
    }

    protected function registerNamespaces(): void
    {
        $this->xpath->registerNamespace('jungle', 'http://example.com/junglebooks/rss/module/1.0/');
    }
}
