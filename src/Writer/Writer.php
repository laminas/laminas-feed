<?php

namespace Laminas\Feed\Writer;

use function in_array;
use function lcfirst;
use function sprintf;
use function trigger_error;

use const E_USER_NOTICE;

class Writer
{
    /**
     * Namespace constants
     */
    public const NAMESPACE_ATOM_03 = 'http://purl.org/atom/ns#';
    public const NAMESPACE_ATOM_10 = 'http://www.w3.org/2005/Atom';
    public const NAMESPACE_RDF     = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
    public const NAMESPACE_RSS_090 = 'http://my.netscape.com/rdf/simple/0.9/';
    public const NAMESPACE_RSS_10  = 'http://purl.org/rss/1.0/';

    /**
     * Feed type constants
     */
    public const TYPE_ANY              = 'any';
    public const TYPE_ATOM_03          = 'atom-03';
    public const TYPE_ATOM_10          = 'atom-10';
    public const TYPE_ATOM_ANY         = 'atom';
    public const TYPE_RSS_090          = 'rss-090';
    public const TYPE_RSS_091          = 'rss-091';
    public const TYPE_RSS_091_NETSCAPE = 'rss-091n';
    public const TYPE_RSS_091_USERLAND = 'rss-091u';
    public const TYPE_RSS_092          = 'rss-092';
    public const TYPE_RSS_093          = 'rss-093';
    public const TYPE_RSS_094          = 'rss-094';
    public const TYPE_RSS_10           = 'rss-10';
    public const TYPE_RSS_20           = 'rss-20';
    public const TYPE_RSS_ANY          = 'rss';

    /** @var ExtensionManagerInterface */
    protected static $extensionManager;

    /**
     * Array of registered extensions by class postfix (after the base class
     * name) across four categories - data containers and renderers for entry
     * and feed levels.
     *
     * @var array
     */
    protected static $extensions = [
        'entry'         => [],
        'feed'          => [],
        'entryRenderer' => [],
        'feedRenderer'  => [],
    ];

    /**
     * Set plugin loader for use with Extensions
     *
     * @return void
     */
    public static function setExtensionManager(ExtensionManagerInterface $extensionManager)
    {
        static::$extensionManager = $extensionManager;
    }

    /**
     * Get plugin manager for use with Extensions
     *
     * @return ExtensionManagerInterface
     */
    public static function getExtensionManager()
    {
        if (! isset(static::$extensionManager)) {
            static::setExtensionManager(new ExtensionManager());
        }
        return static::$extensionManager;
    }

    /**
     * Register an Extension by name
     *
     * @param  string $name
     * @return void
     * @throws Exception\RuntimeException If unable to resolve Extension class.
     */
    public static function registerExtension($name)
    {
        if (! static::hasExtension($name)) {
            throw new Exception\RuntimeException(sprintf(
                'Could not load extension "%s" using Plugin Loader.'
                . ' Check prefix paths are configured and extension exists.',
                $name
            ));
        }

        if (static::isRegistered($name)) {
            return;
        }

        $manager = static::getExtensionManager();

        $feedName = $name . '\Feed';
        if ($manager->has($feedName)) {
            static::$extensions['feed'][] = $feedName;
        }

        $entryName = $name . '\Entry';
        if ($manager->has($entryName)) {
            static::$extensions['entry'][] = $entryName;
        }

        $feedRendererName = $name . '\Renderer\Feed';
        if ($manager->has($feedRendererName)) {
            static::$extensions['feedRenderer'][] = $feedRendererName;
        }

        $entryRendererName = $name . '\Renderer\Entry';
        if ($manager->has($entryRendererName)) {
            static::$extensions['entryRenderer'][] = $entryRendererName;
        }
    }

    /**
     * Is a given named Extension registered?
     *
     * @param  string $extensionName
     * @return bool
     */
    public static function isRegistered($extensionName)
    {
        $feedName          = $extensionName . '\Feed';
        $entryName         = $extensionName . '\Entry';
        $feedRendererName  = $extensionName . '\Renderer\Feed';
        $entryRendererName = $extensionName . '\Renderer\Entry';
        if (
            in_array($feedName, static::$extensions['feed'])
            || in_array($entryName, static::$extensions['entry'])
            || in_array($feedRendererName, static::$extensions['feedRenderer'])
            || in_array($entryRendererName, static::$extensions['entryRenderer'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * Get a list of extensions
     *
     * @return array
     */
    public static function getExtensions()
    {
        return static::$extensions;
    }

    /**
     * Reset class state to defaults
     *
     * @return void
     */
    public static function reset()
    {
        static::$extensionManager = null;
        static::$extensions       = [
            'entry'         => [],
            'feed'          => [],
            'entryRenderer' => [],
            'feedRenderer'  => [],
        ];
    }

    /**
     * Register core (default) extensions
     *
     * @return void
     */
    public static function registerCoreExtensions()
    {
        static::registerExtension('DublinCore');
        static::registerExtension('Content');
        static::registerExtension('Atom');
        static::registerExtension('Slash');
        static::registerExtension('WellFormedWeb');
        static::registerExtension('Threading');
        static::registerExtension('ITunes');

        // Added in 2.10.0; check for it conditionally
        static::hasExtension('GooglePlayPodcast')
            ? static::registerExtension('GooglePlayPodcast')
            : trigger_error(
                sprintf(
                    'Please update your %1$s\ExtensionManagerInterface implementation to add entries for'
                    . ' %1$s\Extension\GooglePlayPodcast\Entry,'
                    . ' %1$s\Extension\GooglePlayPodcast\Feed,'
                    . ' %1$s\Extension\GooglePlayPodcast\Renderer\Entry,'
                    . ' and %1$s\Extension\GooglePlayPodcast\Renderer\Feed.',
                    __NAMESPACE__
                ),
                E_USER_NOTICE
            );

        // Added in development; check for it conditionally
        static::hasExtension('PodcastIndex')
            ? static::registerExtension('PodcastIndex')
            : trigger_error(
                sprintf(
                    'Please update your %1$s\ExtensionManagerInterface implementation to add entries for'
                    . ' %1$s\Extension\PodcastIndex\Entry,'
                    . ' %1$s\Extension\PodcastIndex\Feed,'
                    . ' %1$s\Extension\PodcastIndex\Renderer\Entry,'
                    . ' and %1$s\Extension\PodcastIndex\Renderer\Feed.',
                    __NAMESPACE__
                ),
                E_USER_NOTICE
            );
    }

    /**
     * @deprecated This method is deprecated and will be removed with version 3.0
     *     Use PHP's lcfirst function instead. @see https://php.net/manual/function.lcfirst.php
     *
     * @param  string $str
     * @return string
     */
    public static function lcfirst($str)
    {
        return lcfirst($str);
    }

    /**
     * Does the extension manager have the named extension?
     *
     * This method exists to allow us to test if an extension is present in the
     * extension manager. It may be used by registerExtension() to determine if
     * the extension has items present in the manager, or by
     * registerCoreExtension() to determine if the core extension has entries
     * in the extension manager. In the latter case, this can be useful when
     * adding new extensions in a minor release, as custom extension manager
     * implementations may not yet have an entry for the extension, which would
     * then otherwise cause registerExtension() to fail.
     *
     * @param  string $name
     * @return bool
     */
    protected static function hasExtension($name)
    {
        $manager = static::getExtensionManager();

        $feedName          = $name . '\Feed';
        $entryName         = $name . '\Entry';
        $feedRendererName  = $name . '\Renderer\Feed';
        $entryRendererName = $name . '\Renderer\Entry';

        return $manager->has($feedName)
            || $manager->has($entryName)
            || $manager->has($feedRendererName)
            || $manager->has($entryRendererName);
    }
}
