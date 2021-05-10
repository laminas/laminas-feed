<?php

namespace Laminas\Feed\Reader\Extension\Content;

use Laminas\Feed\Reader;
use Laminas\Feed\Reader\Extension;

class Entry extends Extension\AbstractEntry
{
    public function getContent()
    {
        if ($this->getType() !== Reader\Reader::TYPE_RSS_10
            && $this->getType() !== Reader\Reader::TYPE_RSS_090
        ) {
            $content = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/content:encoded)');
        } else {
            $content = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/content:encoded)');
        }
        return $content;
    }

    /**
     * Register RSS Content Module namespace
     */
    protected function registerNamespaces()
    {
        $this->xpath->registerNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
    }
}
