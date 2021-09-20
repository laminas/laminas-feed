<?php

namespace Laminas\Feed\Reader\Extension\WellFormedWeb;

use Laminas\Feed\Reader\Extension;

use function array_key_exists;

class Entry extends Extension\AbstractEntry
{
    /**
     * Get the entry comment Uri
     *
     * @return null|string
     */
    public function getCommentFeedLink()
    {
        $name = 'commentRss';
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $data = $this->xpath->evaluate('string(' . $this->getXpathPrefix() . '/wfw:' . $name . ')');

        if (! $data) {
            $data = null;
        }

        $this->data[$name] = $data;

        return $data;
    }

    /**
     * Register Slash namespaces
     *
     * @return void
     */
    protected function registerNamespaces()
    {
        $this->xpath->registerNamespace('wfw', 'http://wellformedweb.org/CommentAPI/');
    }
}
