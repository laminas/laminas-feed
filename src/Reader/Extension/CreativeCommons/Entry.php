<?php

namespace Laminas\Feed\Reader\Extension\CreativeCommons;

use Laminas\Feed\Reader\Extension;

class Entry extends Extension\AbstractEntry
{
    /**
     * Get the entry license
     *
     * @param  int $index
     * @return null|string
     */
    public function getLicense($index = 0)
    {
        $licenses = $this->getLicenses();

        if (isset($licenses[$index])) {
            return $licenses[$index];
        }

        return;
    }

    /**
     * Get the entry licenses
     *
     * @return array
     */
    public function getLicenses()
    {
        $name = 'licenses';
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $licenses = [];
        $list     = $this->xpath->evaluate($this->getXpathPrefix() . '//cc:license');

        if ($list->length) {
            foreach ($list as $license) {
                $licenses[] = $license->nodeValue;
            }

            $licenses = array_unique($licenses);
        } else {
            $cc       = new Feed();
            $licenses = $cc->getLicenses();
        }

        $this->data[$name] = $licenses;

        return $this->data[$name];
    }

    /**
     * Register Creative Commons namespaces
     */
    protected function registerNamespaces()
    {
        $this->xpath->registerNamespace('cc', 'http://backend.userland.com/creativeCommonsRssModule');
    }
}
