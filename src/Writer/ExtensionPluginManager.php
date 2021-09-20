<?php

namespace Laminas\Feed\Writer;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Factory\InvokableFactory;

use function get_class;
use function gettype;
use function is_object;
use function sprintf;
use function substr;

/**
 * Plugin manager implementation for feed writer extensions
 *
 * Validation checks that we have an Entry, Feed, or Extension\AbstractRenderer.
 */
class ExtensionPluginManager extends AbstractPluginManager implements ExtensionManagerInterface
{
    /**
     * Aliases for default set of extension classes
     *
     * @var array<array-key, string>
     */
    protected $aliases = [
        // phpcs:disable Generic.Files.LineLength.TooLong
        'atomrendererfeed'                 => Extension\Atom\Renderer\Feed::class,
        'atomRendererFeed'                 => Extension\Atom\Renderer\Feed::class,
        'AtomRendererFeed'                 => Extension\Atom\Renderer\Feed::class,
        'AtomRenderer\Feed'                => Extension\Atom\Renderer\Feed::class,
        'Atom\Renderer\Feed'               => Extension\Atom\Renderer\Feed::class,
        'contentrendererentry'             => Extension\Content\Renderer\Entry::class,
        'contentRendererEntry'             => Extension\Content\Renderer\Entry::class,
        'ContentRendererEntry'             => Extension\Content\Renderer\Entry::class,
        'ContentRenderer\Entry'            => Extension\Content\Renderer\Entry::class,
        'Content\Renderer\Entry'           => Extension\Content\Renderer\Entry::class,
        'dublincorerendererentry'          => Extension\DublinCore\Renderer\Entry::class,
        'dublinCoreRendererEntry'          => Extension\DublinCore\Renderer\Entry::class,
        'DublinCoreRendererEntry'          => Extension\DublinCore\Renderer\Entry::class,
        'DublinCoreRenderer\Entry'         => Extension\DublinCore\Renderer\Entry::class,
        'DublinCore\Renderer\Entry'        => Extension\DublinCore\Renderer\Entry::class,
        'dublincorerendererfeed'           => Extension\DublinCore\Renderer\Feed::class,
        'dublinCoreRendererFeed'           => Extension\DublinCore\Renderer\Feed::class,
        'DublinCoreRendererFeed'           => Extension\DublinCore\Renderer\Feed::class,
        'DublinCoreRenderer\Feed'          => Extension\DublinCore\Renderer\Feed::class,
        'DublinCore\Renderer\Feed'         => Extension\DublinCore\Renderer\Feed::class,
        'googleplaypodcastentry'           => Extension\GooglePlayPodcast\Entry::class,
        'googleplaypodcastEntry'           => Extension\GooglePlayPodcast\Entry::class,
        'googlePlayPodcastEntry'           => Extension\GooglePlayPodcast\Entry::class,
        'GooglePlayPodcastEntry'           => Extension\GooglePlayPodcast\Entry::class,
        'Googleplaypodcast\Entry'          => Extension\GooglePlayPodcast\Entry::class,
        'GooglePlayPodcast\Entry'          => Extension\GooglePlayPodcast\Entry::class,
        'googleplaypodcastfeed'            => Extension\GooglePlayPodcast\Feed::class,
        'googleplaypodcastFeed'            => Extension\GooglePlayPodcast\Feed::class,
        'googlePlayPodcastFeed'            => Extension\GooglePlayPodcast\Feed::class,
        'GooglePlayPodcastFeed'            => Extension\GooglePlayPodcast\Feed::class,
        'Googleplaypodcast\Feed'           => Extension\GooglePlayPodcast\Feed::class,
        'GooglePlayPodcast\Feed'           => Extension\GooglePlayPodcast\Feed::class,
        'googleplaypodcastrendererentry'   => Extension\GooglePlayPodcast\Renderer\Entry::class,
        'googleplaypodcastRendererEntry'   => Extension\GooglePlayPodcast\Renderer\Entry::class,
        'googlePlayPodcastRendererEntry'   => Extension\GooglePlayPodcast\Renderer\Entry::class,
        'GooglePlayPodcastRendererEntry'   => Extension\GooglePlayPodcast\Renderer\Entry::class,
        'GoogleplaypodcastRenderer\Entry'  => Extension\GooglePlayPodcast\Renderer\Entry::class,
        'GooglePlayPodcast\Renderer\Entry' => Extension\GooglePlayPodcast\Renderer\Entry::class,
        'googleplaypodcastrendererfeed'    => Extension\GooglePlayPodcast\Renderer\Feed::class,
        'googleplaypodcastRendererFeed'    => Extension\GooglePlayPodcast\Renderer\Feed::class,
        'googlePlayPodcastRendererFeed'    => Extension\GooglePlayPodcast\Renderer\Feed::class,
        'GooglePlayPodcastRendererFeed'    => Extension\GooglePlayPodcast\Renderer\Feed::class,
        'GoogleplaypodcastRenderer\Feed'   => Extension\GooglePlayPodcast\Renderer\Feed::class,
        'GooglePlayPodcast\Renderer\Feed'  => Extension\GooglePlayPodcast\Renderer\Feed::class,
        'itunesentry'                      => Extension\ITunes\Entry::class,
        'itunesEntry'                      => Extension\ITunes\Entry::class,
        'iTunesEntry'                      => Extension\ITunes\Entry::class,
        'ItunesEntry'                      => Extension\ITunes\Entry::class,
        'Itunes\Entry'                     => Extension\ITunes\Entry::class,
        'ITunes\Entry'                     => Extension\ITunes\Entry::class,
        'itunesfeed'                       => Extension\ITunes\Feed::class,
        'itunesFeed'                       => Extension\ITunes\Feed::class,
        'iTunesFeed'                       => Extension\ITunes\Feed::class,
        'ItunesFeed'                       => Extension\ITunes\Feed::class,
        'Itunes\Feed'                      => Extension\ITunes\Feed::class,
        'ITunes\Feed'                      => Extension\ITunes\Feed::class,
        'itunesrendererentry'              => Extension\ITunes\Renderer\Entry::class,
        'itunesRendererEntry'              => Extension\ITunes\Renderer\Entry::class,
        'iTunesRendererEntry'              => Extension\ITunes\Renderer\Entry::class,
        'ItunesRendererEntry'              => Extension\ITunes\Renderer\Entry::class,
        'ItunesRenderer\Entry'             => Extension\ITunes\Renderer\Entry::class,
        'ITunes\Renderer\Entry'            => Extension\ITunes\Renderer\Entry::class,
        'itunesrendererfeed'               => Extension\ITunes\Renderer\Feed::class,
        'itunesRendererFeed'               => Extension\ITunes\Renderer\Feed::class,
        'iTunesRendererFeed'               => Extension\ITunes\Renderer\Feed::class,
        'ItunesRendererFeed'               => Extension\ITunes\Renderer\Feed::class,
        'ItunesRenderer\Feed'              => Extension\ITunes\Renderer\Feed::class,
        'ITunes\Renderer\Feed'             => Extension\ITunes\Renderer\Feed::class,
        'podcastindexentry'                => Extension\PodcastIndex\Entry::class,
        'podcastindexEntry'                => Extension\PodcastIndex\Entry::class,
        'PodcastIndexEntry'                => Extension\PodcastIndex\Entry::class,
        'PodcastIndex\Entry'               => Extension\PodcastIndex\Entry::class,
        'podcastindexfeed'                 => Extension\PodcastIndex\Feed::class,
        'podcastindexFeed'                 => Extension\PodcastIndex\Feed::class,
        'PodcastIndexFeed'                 => Extension\PodcastIndex\Feed::class,
        'PodcastIndex\Feed'                => Extension\PodcastIndex\Feed::class,
        'podcastindexrendererentry'        => Extension\PodcastIndex\Renderer\Entry::class,
        'podcastindexRendererEntry'        => Extension\PodcastIndex\Renderer\Entry::class,
        'PodcastIndexRendererEntry'        => Extension\PodcastIndex\Renderer\Entry::class,
        'PodcastIndexRenderer\Entry'       => Extension\PodcastIndex\Renderer\Entry::class,
        'PodcastIndex\Renderer\Entry'      => Extension\PodcastIndex\Renderer\Entry::class,
        'podcastindexrendererfeed'         => Extension\PodcastIndex\Renderer\Feed::class,
        'podcastindexRendererFeed'         => Extension\PodcastIndex\Renderer\Feed::class,
        'PodcastIndexRendererFeed'         => Extension\PodcastIndex\Renderer\Feed::class,
        'PodcastIndexRenderer\Feed'        => Extension\PodcastIndex\Renderer\Feed::class,
        'PodcastIndex\Renderer\Feed'       => Extension\PodcastIndex\Renderer\Feed::class,
        'slashrendererentry'               => Extension\Slash\Renderer\Entry::class,
        'slashRendererEntry'               => Extension\Slash\Renderer\Entry::class,
        'SlashRendererEntry'               => Extension\Slash\Renderer\Entry::class,
        'SlashRenderer\Entry'              => Extension\Slash\Renderer\Entry::class,
        'Slash\Renderer\Entry'             => Extension\Slash\Renderer\Entry::class,
        'threadingrendererentry'           => Extension\Threading\Renderer\Entry::class,
        'threadingRendererEntry'           => Extension\Threading\Renderer\Entry::class,
        'ThreadingRendererEntry'           => Extension\Threading\Renderer\Entry::class,
        'ThreadingRenderer\Entry'          => Extension\Threading\Renderer\Entry::class,
        'Threading\Renderer\Entry'         => Extension\Threading\Renderer\Entry::class,
        'wellformedwebrendererentry'       => Extension\WellFormedWeb\Renderer\Entry::class,
        'wellFormedWebRendererEntry'       => Extension\WellFormedWeb\Renderer\Entry::class,
        'WellFormedWebRendererEntry'       => Extension\WellFormedWeb\Renderer\Entry::class,
        'WellFormedWebRenderer\Entry'      => Extension\WellFormedWeb\Renderer\Entry::class,
        'WellFormedWeb\Renderer\Entry'     => Extension\WellFormedWeb\Renderer\Entry::class,

        // Legacy Zend Framework aliases
        // phpcs:disable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName
        \Zend\Feed\Writer\Extension\Atom\Renderer\Feed::class               => Extension\Atom\Renderer\Feed::class,
        \Zend\Feed\Writer\Extension\Content\Renderer\Entry::class           => Extension\Content\Renderer\Entry::class,
        \Zend\Feed\Writer\Extension\DublinCore\Renderer\Entry::class        => Extension\DublinCore\Renderer\Entry::class,
        \Zend\Feed\Writer\Extension\DublinCore\Renderer\Feed::class         => Extension\DublinCore\Renderer\Feed::class,
        \Zend\Feed\Writer\Extension\GooglePlayPodcast\Entry::class          => Extension\GooglePlayPodcast\Entry::class,
        \Zend\Feed\Writer\Extension\GooglePlayPodcast\Feed::class           => Extension\GooglePlayPodcast\Feed::class,
        \Zend\Feed\Writer\Extension\GooglePlayPodcast\Renderer\Entry::class => Extension\GooglePlayPodcast\Renderer\Entry::class,
        \Zend\Feed\Writer\Extension\GooglePlayPodcast\Renderer\Feed::class  => Extension\GooglePlayPodcast\Renderer\Feed::class,
        \Zend\Feed\Writer\Extension\ITunes\Entry::class                     => Extension\ITunes\Entry::class,
        \Zend\Feed\Writer\Extension\ITunes\Feed::class                      => Extension\ITunes\Feed::class,
        \Zend\Feed\Writer\Extension\ITunes\Renderer\Entry::class            => Extension\ITunes\Renderer\Entry::class,
        \Zend\Feed\Writer\Extension\ITunes\Renderer\Feed::class             => Extension\ITunes\Renderer\Feed::class,
        \Zend\Feed\Writer\Extension\Slash\Renderer\Entry::class             => Extension\Slash\Renderer\Entry::class,
        \Zend\Feed\Writer\Extension\Threading\Renderer\Entry::class         => Extension\Threading\Renderer\Entry::class,
        \Zend\Feed\Writer\Extension\WellFormedWeb\Renderer\Entry::class     => Extension\WellFormedWeb\Renderer\Entry::class,
        // phpcs:enable SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly.ReferenceViaFullyQualifiedName

        // v2 normalized FQCNs
        'zendfeedwriterextensionatomrendererfeed'               => Extension\Atom\Renderer\Feed::class,
        'zendfeedwriterextensioncontentrendererentry'           => Extension\Content\Renderer\Entry::class,
        'zendfeedwriterextensiondublincorerendererentry'        => Extension\DublinCore\Renderer\Entry::class,
        'zendfeedwriterextensiondublincorerendererfeed'         => Extension\DublinCore\Renderer\Feed::class,
        'zendfeedwriterextensiongoogleplaypodcastentry'         => Extension\GooglePlayPodcast\Entry::class,
        'zendfeedwriterextensiongoogleplaypodcastfeed'          => Extension\GooglePlayPodcast\Feed::class,
        'zendfeedwriterextensiongoogleplaypodcastrendererentry' => Extension\GooglePlayPodcast\Renderer\Entry::class,
        'zendfeedwriterextensiongoogleplaypodcastrendererfeed'  => Extension\GooglePlayPodcast\Renderer\Feed::class,
        'zendfeedwriterextensionitunesentry'                    => Extension\ITunes\Entry::class,
        'zendfeedwriterextensionitunesfeed'                     => Extension\ITunes\Feed::class,
        'zendfeedwriterextensionitunesrendererentry'            => Extension\ITunes\Renderer\Entry::class,
        'zendfeedwriterextensionitunesrendererfeed'             => Extension\ITunes\Renderer\Feed::class,
        'zendfeedwriterextensionslashrendererentry'             => Extension\Slash\Renderer\Entry::class,
        'zendfeedwriterextensionthreadingrendererentry'         => Extension\Threading\Renderer\Entry::class,
        'zendfeedwriterextensionwellformedwebrendererentry'     => Extension\WellFormedWeb\Renderer\Entry::class,
        // phpcs:enable Generic.Files.LineLength.TooLong
    ];

    /**
     * Factories for default set of extension classes
     *
     * @var array<array-key, callable|string>
     */
    protected $factories = [
        Extension\Atom\Renderer\Feed::class               => InvokableFactory::class,
        Extension\Content\Renderer\Entry::class           => InvokableFactory::class,
        Extension\DublinCore\Renderer\Entry::class        => InvokableFactory::class,
        Extension\DublinCore\Renderer\Feed::class         => InvokableFactory::class,
        Extension\GooglePlayPodcast\Entry::class          => InvokableFactory::class,
        Extension\GooglePlayPodcast\Feed::class           => InvokableFactory::class,
        Extension\GooglePlayPodcast\Renderer\Entry::class => InvokableFactory::class,
        Extension\GooglePlayPodcast\Renderer\Feed::class  => InvokableFactory::class,
        Extension\ITunes\Entry::class                     => InvokableFactory::class,
        Extension\ITunes\Feed::class                      => InvokableFactory::class,
        Extension\ITunes\Renderer\Entry::class            => InvokableFactory::class,
        Extension\ITunes\Renderer\Feed::class             => InvokableFactory::class,
        Extension\PodcastIndex\Entry::class               => InvokableFactory::class,
        Extension\PodcastIndex\Feed::class                => InvokableFactory::class,
        Extension\PodcastIndex\Renderer\Entry::class      => InvokableFactory::class,
        Extension\PodcastIndex\Renderer\Feed::class       => InvokableFactory::class,
        Extension\Slash\Renderer\Entry::class             => InvokableFactory::class,
        Extension\Threading\Renderer\Entry::class         => InvokableFactory::class,
        Extension\WellFormedWeb\Renderer\Entry::class     => InvokableFactory::class,
        // Legacy (v2) due to alias resolution; canonical form of resolved
        // alias is used to look up the factory, while the non-normalized
        // resolved alias is used as the requested name passed to the factory.
        'laminasfeedwriterextensionatomrendererfeed'               => InvokableFactory::class,
        'laminasfeedwriterextensioncontentrendererentry'           => InvokableFactory::class,
        'laminasfeedwriterextensiondublincorerendererentry'        => InvokableFactory::class,
        'laminasfeedwriterextensiondublincorerendererfeed'         => InvokableFactory::class,
        'laminasfeedwriterextensiongoogleplaypodcastentry'         => InvokableFactory::class,
        'laminasfeedwriterextensiongoogleplaypodcastfeed'          => InvokableFactory::class,
        'laminasfeedwriterextensiongoogleplaypodcastrendererentry' => InvokableFactory::class,
        'laminasfeedwriterextensiongoogleplaypodcastrendererfeed'  => InvokableFactory::class,
        'laminasfeedwriterextensionitunesentry'                    => InvokableFactory::class,
        'laminasfeedwriterextensionitunesfeed'                     => InvokableFactory::class,
        'laminasfeedwriterextensionitunesrendererentry'            => InvokableFactory::class,
        'laminasfeedwriterextensionitunesrendererfeed'             => InvokableFactory::class,
        'laminasfeedwriterextensionpodcastindexentry'              => InvokableFactory::class,
        'laminasfeedwriterextensionpodcastindexfeed'               => InvokableFactory::class,
        'laminasfeedwriterextensionpodcastindexrendererentry'      => InvokableFactory::class,
        'laminasfeedwriterextensionpodcastindexrendererfeed'       => InvokableFactory::class,
        'laminasfeedwriterextensionslashrendererentry'             => InvokableFactory::class,
        'laminasfeedwriterextensionthreadingrendererentry'         => InvokableFactory::class,
        'laminasfeedwriterextensionwellformedwebrendererentry'     => InvokableFactory::class,
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
     * Validate the plugin (v3)
     *
     * Checks that the extension loaded is of a valid type.
     *
     * @param  object $instance
     * @return void
     * @throws InvalidServiceException If invalid.
     */
    public function validate($instance)
    {
        if ($instance instanceof Extension\AbstractRenderer) {
            // we're okay
            return;
        }

        if ('Feed' === substr(get_class($instance), -4)) {
            // we're okay
            return;
        }

        if ('Entry' === substr(get_class($instance), -5)) {
            // we're okay
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Extension\RendererInterface '
            . 'or the classname must end in "Feed" or "Entry"',
            is_object($instance) ? get_class($instance) : gettype($instance),
            __NAMESPACE__
        ));
    }

    /**
     * Validate plugin (v2)
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidArgumentException When invalid.
     */
    public function validatePlugin($plugin)
    {
        try {
            $this->validate($plugin);
        } catch (InvalidServiceException $e) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Plugin of type %s is invalid; must implement %s\Extension\RendererInterface '
                . 'or the classname must end in "Feed" or "Entry"',
                is_object($plugin) ? get_class($plugin) : gettype($plugin),
                __NAMESPACE__
            ));
        }
    }
}
