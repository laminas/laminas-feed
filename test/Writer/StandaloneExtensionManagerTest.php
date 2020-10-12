<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Feed\Writer;

use Laminas\Feed\Writer\Exception\InvalidArgumentException;
use Laminas\Feed\Writer\Extension;
use Laminas\Feed\Writer\ExtensionManagerInterface;
use Laminas\Feed\Writer\StandaloneExtensionManager;
use PHPUnit\Framework\TestCase;

class StandaloneExtensionManagerTest extends TestCase
{
    /**
     * @var StandaloneExtensionManager
     */
    private $extensions;

    protected function setUp(): void
    {
        $this->extensions = new StandaloneExtensionManager();
    }

    public function testIsAnExtensionManagerImplementation()
    {
        $this->assertInstanceOf(ExtensionManagerInterface::class, $this->extensions);
    }

    public function defaultPlugins()
    {
        return [
            'Atom\Renderer\Feed'           => ['Atom\Renderer\Feed', Extension\Atom\Renderer\Feed::class],
            'Content\Renderer\Entry'       => ['Content\Renderer\Entry', Extension\Content\Renderer\Entry::class],
            'DublinCore\Renderer\Entry'    => ['DublinCore\Renderer\Entry', Extension\DublinCore\Renderer\Entry::class],
            'DublinCore\Renderer\Feed'     => ['DublinCore\Renderer\Feed', Extension\DublinCore\Renderer\Feed::class],
            'ITunes\Entry'                 => ['ITunes\Entry', Extension\ITunes\Entry::class],
            'ITunes\Feed'                  => ['ITunes\Feed', Extension\ITunes\Feed::class],
            'ITunes\Renderer\Entry'        => ['ITunes\Renderer\Entry', Extension\ITunes\Renderer\Entry::class],
            'ITunes\Renderer\Feed'         => ['ITunes\Renderer\Feed', Extension\ITunes\Renderer\Feed::class],
            'Slash\Renderer\Entry'         => ['Slash\Renderer\Entry', Extension\Slash\Renderer\Entry::class],
            'Threading\Renderer\Entry'     => ['Threading\Renderer\Entry', Extension\Threading\Renderer\Entry::class],
            'WellFormedWeb\Renderer\Entry' => [
                'WellFormedWeb\Renderer\Entry',
                Extension\WellFormedWeb\Renderer\Entry::class,
            ],
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

    public function testAddAcceptsValidExtensionClasses()
    {
        $this->extensions->add('Test/Feed', 'MyTestExtension_Feed');
        $this->assertTrue($this->extensions->has('Test/Feed'));

        $this->extensions->add('Test/Entry', 'MyTestExtension_Entry');
        $this->assertTrue($this->extensions->has('Test/Entry'));

        $ext = $this->prophesize(Extension\AbstractRenderer::class)->reveal();
        $this->extensions->add('Test/Thing', get_class($ext));
        $this->assertTrue($this->extensions->has('Test/Thing'));
    }

    public function testAddRejectsInvalidExtensions()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->extensions->add('Test/Feed', 'blah');
    }

    public function testExtensionRemoval()
    {
        $this->extensions->add('Test/Entry', 'MyTestExtension_Entry');
        $this->assertTrue($this->extensions->has('Test/Entry'));
        $this->extensions->remove('Test/Entry');
        $this->assertFalse($this->extensions->has('Test/Entry'));
    }
}
