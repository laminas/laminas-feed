<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */
namespace LaminasTest\Feed\Reader;

use Laminas\Feed\Reader\StandaloneExtensionManager;
use PHPUnit_Framework_TestCase as TestCase;

class StandaloneExtensionManagerTest extends TestCase
{
    public function setUp()
    {
        $this->extensions = new StandaloneExtensionManager();
    }

    public function testIsAnExtensionManagerImplementation()
    {
        $this->assertInstanceOf('Laminas\Feed\Reader\ExtensionManagerInterface', $this->extensions);
    }

    public function defaultPlugins()
    {
        return array(
            'Atom\Entry'            => array('Atom\Entry', 'Laminas\Feed\Reader\Extension\Atom\Entry'),
            'Atom\Feed'             => array('Atom\Feed', 'Laminas\Feed\Reader\Extension\Atom\Feed'),
            'Content\Entry'         => array('Content\Entry', 'Laminas\Feed\Reader\Extension\Content\Entry'),
            'CreativeCommons\Entry' => array(
                'CreativeCommons\Entry',
                'Laminas\Feed\Reader\Extension\CreativeCommons\Entry'
            ),
            'CreativeCommons\Feed'  => array('CreativeCommons\Feed', 'Laminas\Feed\Reader\Extension\CreativeCommons\Feed'),
            'DublinCore\Entry'      => array('DublinCore\Entry', 'Laminas\Feed\Reader\Extension\DublinCore\Entry'),
            'DublinCore\Feed'       => array('DublinCore\Feed', 'Laminas\Feed\Reader\Extension\DublinCore\Feed'),
            'Podcast\Entry'         => array('Podcast\Entry', 'Laminas\Feed\Reader\Extension\Podcast\Entry'),
            'Podcast\Feed'          => array('Podcast\Feed', 'Laminas\Feed\Reader\Extension\Podcast\Feed'),
            'Slash\Entry'           => array('Slash\Entry', 'Laminas\Feed\Reader\Extension\Slash\Entry'),
            'Syndication\Feed'      => array('Syndication\Feed', 'Laminas\Feed\Reader\Extension\Syndication\Feed'),
            'Thread\Entry'          => array('Thread\Entry', 'Laminas\Feed\Reader\Extension\Thread\Entry'),
            'WellFormedWeb\Entry'   => array('WellFormedWeb\Entry', 'Laminas\Feed\Reader\Extension\WellFormedWeb\Entry'),
        );
    }

    /**
     * @dataProvider defaultPlugins
     */
    public function testHasAllDefaultPlugins($pluginName, $pluginClass)
    {
        $this->assertTrue($this->extensions->has($pluginName));
    }

    /**
     * @dataProvider defaultPlugins
     */
    public function testCanRetrieveDefaultPluginInstances($pluginName, $pluginClass)
    {
        $extension = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $extension);
    }

    /**
     * @dataProvider defaultPlugins
     */
    public function testEachPluginRetrievalReturnsNewInstance($pluginName, $pluginClass)
    {
        $extension = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $extension);

        $test = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $test);
        $this->assertNotSame($extension, $test);
    }
}
