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
        return [
            'Atom\Entry'            => ['Atom\Entry', 'Laminas\Feed\Reader\Extension\Atom\Entry'],
            'Atom\Feed'             => ['Atom\Feed', 'Laminas\Feed\Reader\Extension\Atom\Feed'],
            'Content\Entry'         => ['Content\Entry', 'Laminas\Feed\Reader\Extension\Content\Entry'],
            'CreativeCommons\Entry' => [
                'CreativeCommons\Entry',
                'Laminas\Feed\Reader\Extension\CreativeCommons\Entry'
            ],
            'CreativeCommons\Feed'  => ['CreativeCommons\Feed', 'Laminas\Feed\Reader\Extension\CreativeCommons\Feed'],
            'DublinCore\Entry'      => ['DublinCore\Entry', 'Laminas\Feed\Reader\Extension\DublinCore\Entry'],
            'DublinCore\Feed'       => ['DublinCore\Feed', 'Laminas\Feed\Reader\Extension\DublinCore\Feed'],
            'Podcast\Entry'         => ['Podcast\Entry', 'Laminas\Feed\Reader\Extension\Podcast\Entry'],
            'Podcast\Feed'          => ['Podcast\Feed', 'Laminas\Feed\Reader\Extension\Podcast\Feed'],
            'Slash\Entry'           => ['Slash\Entry', 'Laminas\Feed\Reader\Extension\Slash\Entry'],
            'Syndication\Feed'      => ['Syndication\Feed', 'Laminas\Feed\Reader\Extension\Syndication\Feed'],
            'Thread\Entry'          => ['Thread\Entry', 'Laminas\Feed\Reader\Extension\Thread\Entry'],
            'WellFormedWeb\Entry'   => ['WellFormedWeb\Entry', 'Laminas\Feed\Reader\Extension\WellFormedWeb\Entry'],
        ];
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
