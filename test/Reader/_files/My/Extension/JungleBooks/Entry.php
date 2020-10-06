<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace My\Extension\JungleBooks;

use Laminas\Feed\Reader\Extension;

class Entry extends Extension\AbstractEntry
{
    public function getIsbn()
    {
        if (isset($this->data['isbn'])) {
            return $this->data['isbn'];
        }
        $isbn = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/jungle:isbn)');
        if (! $isbn) {
            $isbn = null;
        }
        $this->data['isbn'] = $title;
        return $this->data['isbn'];
    }

    protected function registerNamespaces(): void
    {
        $this->xpath->registerNamespace('jungle', 'http://example.com/junglebooks/rss/module/1.0/');
    }
}
