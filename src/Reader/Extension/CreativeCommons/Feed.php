<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Reader\Extension\CreativeCommons;

use Laminas\Feed\Reader;
use Laminas\Feed\Reader\Extension;

/**
* @category Laminas
* @package Reader\Reader
*/
class Feed extends Extension\AbstractFeed
{
    /**
     * Get the entry license
     *
     * @return string|null
     */
    public function getLicense($index = 0)
    {
        $licenses = $this->getLicenses();

        if (isset($licenses[$index])) {
            return $licenses[$index];
        }

        return null;
    }

    /**
     * Get the entry licenses
     *
     * @return array
     */
    public function getLicenses()
    {
        $name = 'licenses';
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        }

        $licenses = array();
        $list = $this->_xpath->evaluate('channel/cc:license');

        if ($list->length) {
            foreach ($list as $license) {
                $licenses[] = $license->nodeValue;
            }

            $licenses = array_unique($licenses);
        }

        $this->_data[$name] = $licenses;

        return $this->_data[$name];
    }

    /**
     * Register Creative Commons namespaces
     *
     * @return void
     */
    protected function _registerNamespaces()
    {
        $this->_xpath->registerNamespace('cc', 'http://backend.userland.com/creativeCommonsRssModule');
    }
}
