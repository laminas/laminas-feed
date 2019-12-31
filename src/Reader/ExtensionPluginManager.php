<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Reader;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Factory\InvokableFactory;

/**
 * Plugin manager implementation for feed reader extensions based on the
 * AbstractPluginManager.
 *
 * Validation checks that we have an Extension\AbstractEntry or
 * Extension\AbstractFeed.
 */
class ExtensionPluginManager extends AbstractPluginManager implements ExtensionManagerInterface
{
    /**
     * Aliases for default set of extension classes
     *
     * @var array
     */
    protected $aliases = [
        'atomentry'            => Extension\Atom\Entry::class,
        'atomEntry'            => Extension\Atom\Entry::class,
        'AtomEntry'            => Extension\Atom\Entry::class,
        'Atom\Entry'           => Extension\Atom\Entry::class,
        'atomfeed'             => Extension\Atom\Feed::class,
        'atomFeed'             => Extension\Atom\Feed::class,
        'AtomFeed'             => Extension\Atom\Feed::class,
        'Atom\Feed'            => Extension\Atom\Feed::class,
        'contententry'         => Extension\Content\Entry::class,
        'contentEntry'         => Extension\Content\Entry::class,
        'ContentEntry'         => Extension\Content\Entry::class,
        'Content\Entry'        => Extension\Content\Entry::class,
        'creativecommonsentry' => Extension\CreativeCommons\Entry::class,
        'creativeCommonsEntry' => Extension\CreativeCommons\Entry::class,
        'CreativeCommonsEntry' => Extension\CreativeCommons\Entry::class,
        'CreativeCommons\Entry' => Extension\CreativeCommons\Entry::class,
        'creativecommonsfeed'  => Extension\CreativeCommons\Feed::class,
        'creativeCommonsFeed'  => Extension\CreativeCommons\Feed::class,
        'CreativeCommonsFeed'  => Extension\CreativeCommons\Feed::class,
        'CreativeCommons\Feed' => Extension\CreativeCommons\Feed::class,
        'dublincoreentry'      => Extension\DublinCore\Entry::class,
        'dublinCoreEntry'      => Extension\DublinCore\Entry::class,
        'DublinCoreEntry'      => Extension\DublinCore\Entry::class,
        'DublinCore\Entry'     => Extension\DublinCore\Entry::class,
        'dublincorefeed'       => Extension\DublinCore\Feed::class,
        'dublinCoreFeed'       => Extension\DublinCore\Feed::class,
        'DublinCoreFeed'       => Extension\DublinCore\Feed::class,
        'DublinCore\Feed'      => Extension\DublinCore\Feed::class,
        'googleplaypodcastentry'  => Extension\GooglePlayPodcast\Entry::class,
        'googlePlayPodcastEntry'  => Extension\GooglePlayPodcast\Entry::class,
        'GooglePlayPodcastEntry'  => Extension\GooglePlayPodcast\Entry::class,
        'GooglePlayPodcast\Entry' => Extension\GooglePlayPodcast\Entry::class,
        'googleplaypodcastfeed'   => Extension\GooglePlayPodcast\Feed::class,
        'googlePlayPodcastFeed'   => Extension\GooglePlayPodcast\Feed::class,
        'GooglePlayPodcastFeed'   => Extension\GooglePlayPodcast\Feed::class,
        'GooglePlayPodcast\Feed'  => Extension\GooglePlayPodcast\Feed::class,
        'podcastentry'         => Extension\Podcast\Entry::class,
        'podcastEntry'         => Extension\Podcast\Entry::class,
        'PodcastEntry'         => Extension\Podcast\Entry::class,
        'Podcast\Entry'        => Extension\Podcast\Entry::class,
        'podcastfeed'          => Extension\Podcast\Feed::class,
        'podcastFeed'          => Extension\Podcast\Feed::class,
        'PodcastFeed'          => Extension\Podcast\Feed::class,
        'Podcast\Feed'         => Extension\Podcast\Feed::class,
        'slashentry'           => Extension\Slash\Entry::class,
        'slashEntry'           => Extension\Slash\Entry::class,
        'SlashEntry'           => Extension\Slash\Entry::class,
        'Slash\Entry'          => Extension\Slash\Entry::class,
        'syndicationfeed'      => Extension\Syndication\Feed::class,
        'syndicationFeed'      => Extension\Syndication\Feed::class,
        'SyndicationFeed'      => Extension\Syndication\Feed::class,
        'Syndication\Feed'     => Extension\Syndication\Feed::class,
        'threadentry'          => Extension\Thread\Entry::class,
        'threadEntry'          => Extension\Thread\Entry::class,
        'ThreadEntry'          => Extension\Thread\Entry::class,
        'Thread\Entry'         => Extension\Thread\Entry::class,
        'wellformedwebentry'   => Extension\WellFormedWeb\Entry::class,
        'wellFormedWebEntry'   => Extension\WellFormedWeb\Entry::class,
        'WellFormedWebEntry'   => Extension\WellFormedWeb\Entry::class,
        'WellFormedWeb\Entry'  => Extension\WellFormedWeb\Entry::class,

        // Legacy Zend Framework aliases
        \Zend\Feed\Reader\Extension\Atom\Entry::class => Extension\Atom\Entry::class,
        \Zend\Feed\Reader\Extension\Atom\Feed::class => Extension\Atom\Feed::class,
        \Zend\Feed\Reader\Extension\Content\Entry::class => Extension\Content\Entry::class,
        \Zend\Feed\Reader\Extension\CreativeCommons\Entry::class => Extension\CreativeCommons\Entry::class,
        \Zend\Feed\Reader\Extension\CreativeCommons\Feed::class => Extension\CreativeCommons\Feed::class,
        \Zend\Feed\Reader\Extension\DublinCore\Entry::class => Extension\DublinCore\Entry::class,
        \Zend\Feed\Reader\Extension\DublinCore\Feed::class => Extension\DublinCore\Feed::class,
        \Zend\Feed\Reader\Extension\GooglePlayPodcast\Entry::class => Extension\GooglePlayPodcast\Entry::class,
        \Zend\Feed\Reader\Extension\GooglePlayPodcast\Feed::class => Extension\GooglePlayPodcast\Feed::class,
        \Zend\Feed\Reader\Extension\Podcast\Entry::class => Extension\Podcast\Entry::class,
        \Zend\Feed\Reader\Extension\Podcast\Feed::class => Extension\Podcast\Feed::class,
        \Zend\Feed\Reader\Extension\Slash\Entry::class => Extension\Slash\Entry::class,
        \Zend\Feed\Reader\Extension\Syndication\Feed::class => Extension\Syndication\Feed::class,
        \Zend\Feed\Reader\Extension\Thread\Entry::class => Extension\Thread\Entry::class,
        \Zend\Feed\Reader\Extension\WellFormedWeb\Entry::class => Extension\WellFormedWeb\Entry::class,

        // v2 normalized FQCNs
        'zendfeedreaderextensionatomentry' => Extension\Atom\Entry::class,
        'zendfeedreaderextensionatomfeed' => Extension\Atom\Feed::class,
        'zendfeedreaderextensioncontententry' => Extension\Content\Entry::class,
        'zendfeedreaderextensioncreativecommonsentry' => Extension\CreativeCommons\Entry::class,
        'zendfeedreaderextensioncreativecommonsfeed' => Extension\CreativeCommons\Feed::class,
        'zendfeedreaderextensiondublincoreentry' => Extension\DublinCore\Entry::class,
        'zendfeedreaderextensiondublincorefeed' => Extension\DublinCore\Feed::class,
        'zendfeedreaderextensiongoogleplaypodcastentry' => Extension\GooglePlayPodcast\Entry::class,
        'zendfeedreaderextensiongoogleplaypodcastfeed' => Extension\GooglePlayPodcast\Feed::class,
        'zendfeedreaderextensionpodcastentry' => Extension\Podcast\Entry::class,
        'zendfeedreaderextensionpodcastfeed' => Extension\Podcast\Feed::class,
        'zendfeedreaderextensionslashentry' => Extension\Slash\Entry::class,
        'zendfeedreaderextensionsyndicationfeed' => Extension\Syndication\Feed::class,
        'zendfeedreaderextensionthreadentry' => Extension\Thread\Entry::class,
        'zendfeedreaderextensionwellformedwebentry' => Extension\WellFormedWeb\Entry::class,
    ];

    /**
     * Factories for default set of extension classes
     *
     * @var array
     */
    protected $factories = [
        Extension\Atom\Entry::class            => InvokableFactory::class,
        Extension\Atom\Feed::class             => InvokableFactory::class,
        Extension\Content\Entry::class         => InvokableFactory::class,
        Extension\CreativeCommons\Entry::class => InvokableFactory::class,
        Extension\CreativeCommons\Feed::class  => InvokableFactory::class,
        Extension\DublinCore\Entry::class      => InvokableFactory::class,
        Extension\DublinCore\Feed::class       => InvokableFactory::class,
        Extension\GooglePlayPodcast\Entry::class => InvokableFactory::class,
        Extension\GooglePlayPodcast\Feed::class  => InvokableFactory::class,
        Extension\Podcast\Entry::class         => InvokableFactory::class,
        Extension\Podcast\Feed::class          => InvokableFactory::class,
        Extension\Slash\Entry::class           => InvokableFactory::class,
        Extension\Syndication\Feed::class      => InvokableFactory::class,
        Extension\Thread\Entry::class          => InvokableFactory::class,
        Extension\WellFormedWeb\Entry::class   => InvokableFactory::class,
        // Legacy (v2) due to alias resolution; canonical form of resolved
        // alias is used to look up the factory, while the non-normalized
        // resolved alias is used as the requested name passed to the factory.
        'laminasfeedreaderextensionatomentry'            => InvokableFactory::class,
        'laminasfeedreaderextensionatomfeed'             => InvokableFactory::class,
        'laminasfeedreaderextensioncontententry'         => InvokableFactory::class,
        'laminasfeedreaderextensioncreativecommonsentry' => InvokableFactory::class,
        'laminasfeedreaderextensioncreativecommonsfeed'  => InvokableFactory::class,
        'laminasfeedreaderextensiondublincoreentry'      => InvokableFactory::class,
        'laminasfeedreaderextensiondublincorefeed'       => InvokableFactory::class,
        'laminasfeedreaderextensiongoogleplaypodcastentry' => InvokableFactory::class,
        'laminasfeedreaderextensiongoogleplaypodcastfeed'  => InvokableFactory::class,
        'laminasfeedreaderextensionpodcastentry'         => InvokableFactory::class,
        'laminasfeedreaderextensionpodcastfeed'          => InvokableFactory::class,
        'laminasfeedreaderextensionslashentry'           => InvokableFactory::class,
        'laminasfeedreaderextensionsyndicationfeed'      => InvokableFactory::class,
        'laminasfeedreaderextensionthreadentry'          => InvokableFactory::class,
        'laminasfeedreaderextensionwellformedwebentry'   => InvokableFactory::class,
    ];

    /**
     * Do not share instances (v2)
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * Do not share instances (v3)
     *
     * @var bool
     */
    protected $sharedByDefault = false;

    /**
     * Validate the plugin
     *
     * Checks that the extension loaded is of a valid type.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validate($plugin)
    {
        if ($plugin instanceof Extension\AbstractEntry
            || $plugin instanceof Extension\AbstractFeed
        ) {
            // we're okay
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Extension\AbstractFeed '
            . 'or %s\Extension\AbstractEntry',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__,
            __NAMESPACE__
        ));
    }

    /**
     * Validate the plugin (v2)
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validatePlugin($plugin)
    {
        try {
            $this->validate($plugin);
        } catch (InvalidServiceException $e) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Plugin of type %s is invalid; must implement %s\Extension\AbstractFeed '
                . 'or %s\Extension\AbstractEntry',
                (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
                __NAMESPACE__,
                __NAMESPACE__
            ));
        }
    }
}
