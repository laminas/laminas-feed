<?php

namespace Laminas\Feed\Reader;

use Laminas\Feed\Reader\Exception\InvalidArgumentException;

class StandaloneExtensionManager implements ExtensionManagerInterface
{
    private $extensions = [
        'Atom\Entry'              => Extension\Atom\Entry::class,
        'Atom\Feed'               => Extension\Atom\Feed::class,
        'Content\Entry'           => Extension\Content\Entry::class,
        'CreativeCommons\Entry'   => Extension\CreativeCommons\Entry::class,
        'CreativeCommons\Feed'    => Extension\CreativeCommons\Feed::class,
        'DublinCore\Entry'        => Extension\DublinCore\Entry::class,
        'DublinCore\Feed'         => Extension\DublinCore\Feed::class,
        'GooglePlayPodcast\Entry' => Extension\GooglePlayPodcast\Entry::class,
        'GooglePlayPodcast\Feed'  => Extension\GooglePlayPodcast\Feed::class,
        'Podcast\Entry'           => Extension\Podcast\Entry::class,
        'Podcast\Feed'            => Extension\Podcast\Feed::class,
        'PodcastIndex\Entry'      => Extension\PodcastIndex\Entry::class,
        'PodcastIndex\Feed'       => Extension\PodcastIndex\Feed::class,
        'Slash\Entry'             => Extension\Slash\Entry::class,
        'Syndication\Feed'        => Extension\Syndication\Feed::class,
        'Thread\Entry'            => Extension\Thread\Entry::class,
        'WellFormedWeb\Entry'     => Extension\WellFormedWeb\Entry::class,
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

    /**
     * Add an extension.
     *
     * @param string $name
     * @param string $class
     * @return void
     */
    public function add($name, $class)
    {
        if (is_string($class)
            && (is_a($class, Extension\AbstractEntry::class, true)
                || is_a($class, Extension\AbstractFeed::class, true))
        ) {
            $this->extensions[$name] = $class;
            return;
        }

        throw new InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement %2$s\Extension\AbstractFeed '
            . 'or %2$s\Extension\AbstractEntry',
            $class,
            __NAMESPACE__
        ));
    }

    /**
     * Remove an extension.
     *
     * @param string $name
     * @return void
     */
    public function remove($name)
    {
        unset($this->extensions[$name]);
    }
}
