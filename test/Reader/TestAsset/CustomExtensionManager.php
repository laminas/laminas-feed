<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Reader\TestAsset;

use Laminas\Feed\Reader\Extension;
use Laminas\Feed\Reader\ExtensionManagerInterface;

/**
 * Standalone extension manager that omits any extensions added after the 2.9 series.
 */
class CustomExtensionManager implements ExtensionManagerInterface
{
    private $extensions = [
        'Atom\Entry'            => Extension\Atom\Entry::class,
        'Atom\Feed'             => Extension\Atom\Feed::class,
        'Content\Entry'         => Extension\Content\Entry::class,
        'CreativeCommons\Entry' => Extension\CreativeCommons\Entry::class,
        'CreativeCommons\Feed'  => Extension\CreativeCommons\Feed::class,
        'DublinCore\Entry'      => Extension\DublinCore\Entry::class,
        'DublinCore\Feed'       => Extension\DublinCore\Feed::class,
        'Podcast\Entry'         => Extension\Podcast\Entry::class,
        'Podcast\Feed'          => Extension\Podcast\Feed::class,
        'Slash\Entry'           => Extension\Slash\Entry::class,
        'Syndication\Feed'      => Extension\Syndication\Feed::class,
        'Thread\Entry'          => Extension\Thread\Entry::class,
        'WellFormedWeb\Entry'   => Extension\WellFormedWeb\Entry::class,
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
