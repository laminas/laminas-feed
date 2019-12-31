<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Reader;

use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for feed reader extensions based on the
 * AbstractPluginManager.
 *
 * Validation checks that we have an Extension\AbstractEntry or
 * Extension\AbstractFeed.
 */
class ExtensionPluginManager extends AbstractPluginManager
{
    /**
     * Default set of extension classes
     *
     * @var array
     */
    protected $invokableClasses = array(
        'atomentry'            => 'Laminas\Feed\Reader\Extension\Atom\Entry',
        'atomfeed'             => 'Laminas\Feed\Reader\Extension\Atom\Feed',
        'contententry'         => 'Laminas\Feed\Reader\Extension\Content\Entry',
        'creativecommonsentry' => 'Laminas\Feed\Reader\Extension\CreativeCommons\Entry',
        'creativecommonsfeed'  => 'Laminas\Feed\Reader\Extension\CreativeCommons\Feed',
        'dublincoreentry'      => 'Laminas\Feed\Reader\Extension\DublinCore\Entry',
        'dublincorefeed'       => 'Laminas\Feed\Reader\Extension\DublinCore\Feed',
        'podcastentry'         => 'Laminas\Feed\Reader\Extension\Podcast\Entry',
        'podcastfeed'          => 'Laminas\Feed\Reader\Extension\Podcast\Feed',
        'slashentry'           => 'Laminas\Feed\Reader\Extension\Slash\Entry',
        'syndicationfeed'      => 'Laminas\Feed\Reader\Extension\Syndication\Feed',
        'threadentry'          => 'Laminas\Feed\Reader\Extension\Thread\Entry',
        'wellformedwebentry'   => 'Laminas\Feed\Reader\Extension\WellFormedWeb\Entry',
    );

    /**
     * Do not share instances
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * Validate the plugin
     *
     * Checks that the extension loaded is of a valid type.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Extension\AbstractEntry
            || $plugin instanceof Extension\AbstractFeed
        ) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Extension\AbstractFeed '
            . 'or %s\Extension\AbstractEntry',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__,
            __NAMESPACE__
        ));
    }
}
