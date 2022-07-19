<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader\Extension\Content;

use Laminas\Feed\Reader;
use Laminas\Feed\Reader\Extension;

class Entry extends Extension\AbstractEntry
{
    /** @return string */
    public function getContent()
    {
        if (
            $this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            return $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/content:encoded)');
        }

        return $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/content:encoded)');
    }

    /**
     * Register RSS Content Module namespace
     */
    protected function registerNamespaces()
    {
        $this->xpath->registerNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
    }
}
