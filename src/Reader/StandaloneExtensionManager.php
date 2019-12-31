<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Reader;

class StandaloneExtensionManager implements ExtensionManagerInterface
{
    private $extensions = [
        'Atom\Entry'            => 'Laminas\Feed\Reader\Extension\Atom\Entry',
        'Atom\Feed'             => 'Laminas\Feed\Reader\Extension\Atom\Feed',
        'Content\Entry'         => 'Laminas\Feed\Reader\Extension\Content\Entry',
        'CreativeCommons\Entry' => 'Laminas\Feed\Reader\Extension\CreativeCommons\Entry',
        'CreativeCommons\Feed'  => 'Laminas\Feed\Reader\Extension\CreativeCommons\Feed',
        'DublinCore\Entry'      => 'Laminas\Feed\Reader\Extension\DublinCore\Entry',
        'DublinCore\Feed'       => 'Laminas\Feed\Reader\Extension\DublinCore\Feed',
        'Podcast\Entry'         => 'Laminas\Feed\Reader\Extension\Podcast\Entry',
        'Podcast\Feed'          => 'Laminas\Feed\Reader\Extension\Podcast\Feed',
        'Slash\Entry'           => 'Laminas\Feed\Reader\Extension\Slash\Entry',
        'Syndication\Feed'      => 'Laminas\Feed\Reader\Extension\Syndication\Feed',
        'Thread\Entry'          => 'Laminas\Feed\Reader\Extension\Thread\Entry',
        'WellFormedWeb\Entry'   => 'Laminas\Feed\Reader\Extension\WellFormedWeb\Entry',
    ];

    /**
     * Do we have the extension?
     *
     * @param  string $extension
     * @return bool
     */
    public function has($extension)
    {
        return array_key_exists($extension, $this->extensions);
    }

    /**
     * Retrieve the extension
     *
     * @param  string $extension
     * @return Extension\AbstractEntry|Extension\AbstractFeed
     */
    public function get($extension)
    {
        $class = $this->extensions[$extension];
        return new $class();
    }
}
