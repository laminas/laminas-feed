<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Writer;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ConfigInterface;

/**
 * Plugin manager implementation for feed writer extensions
 *
 * Validation checks that we have an Entry, Feed, or Extension\AbstractRenderer.
 *
 * @category   Laminas
 * @package    Laminas_Feed_Writer
 */
class ExtensionManager extends AbstractPluginManager
{
    /**
     * Default set of extension classes
     *
     * @var array
     */
    protected $invokableClasses = array(
        'atomrendererfeed'           => 'Laminas\Feed\Writer\Extension\Atom\Renderer\Feed',
        'contentrendererentry'       => 'Laminas\Feed\Writer\Extension\Content\Renderer\Entry',
        'dublincorerendererentry'    => 'Laminas\Feed\Writer\Extension\DublinCore\Renderer\Entry',
        'dublincorerendererfeed'     => 'Laminas\Feed\Writer\Extension\DublinCore\Renderer\Feed',
        'itunesentry'                => 'Laminas\Feed\Writer\Extension\ITunes\Entry',
        'itunesfeed'                 => 'Laminas\Feed\Writer\Extension\ITunes\Feed',
        'itunesrendererentry'        => 'Laminas\Feed\Writer\Extension\ITunes\Renderer\Entry',
        'itunesrendererfeed'         => 'Laminas\Feed\Writer\Extension\ITunes\Renderer\Feed',
        'slashrendererentry'         => 'Laminas\Feed\Writer\Extension\Slash\Renderer\Entry',
        'threadingrendererentry'     => 'Laminas\Feed\Writer\Extension\Threading\Renderer\Entry',
        'wellformedwebrendererentry' => 'Laminas\Feed\Writer\Extension\WellFormedWeb\Renderer\Entry',
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
        if ($plugin instanceof Extension\AbstractRenderer) {
            // we're okay
            return;
        }

        if ('Feed' == substr(get_class($plugin), -4)) {
            // we're okay
            return;
        }

        if ('Entry' == substr(get_class($plugin), -5)) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Extension\RendererInterface '
            . 'or the classname must end in "Feed" or "Entry"',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
