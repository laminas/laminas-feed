<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace My\Extension\JungleBooks;

use Laminas\Feed\Reader\Extension;

class Feed extends Extension\AbstractFeed
{
    public function getDaysPopularBookLink()
    {
        if (isset($this->data['dayPopular'])) {
            return $this->data['dayPopular'];
        }
        $dayPopular = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/jungle:dayPopular)');
        if (! $dayPopular) {
            $dayPopular = null;
        }
        $this->data['dayPopular'] = $dayPopular;
        return $this->data['dayPopular'];
    }

    protected function registerNamespaces(): void
    {
        $this->xpath->registerNamespace('jungle', 'http://example.com/junglebooks/rss/module/1.0/');
    }
}
