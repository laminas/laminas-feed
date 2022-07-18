<?php

declare(strict_types=1);

namespace My\Extension\JungleBooks;

use Laminas\Feed\Reader\Extension;

use function is_string;

class Feed extends Extension\AbstractFeed
{
    /** @return null|string */
    public function getDaysPopularBookLink()
    {
        if (isset($this->data['dayPopular'])) {
            return $this->data['dayPopular'];
        }

        $dayPopular = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/jungle:dayPopular)');
        if (! is_string($dayPopular)) {
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
