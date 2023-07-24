<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Writer;

use Laminas\Feed\Writer\Exception\InvalidArgumentException;
use Laminas\Feed\Writer\Extension;
use Laminas\Feed\Writer\ExtensionManagerInterface;
use Laminas\Feed\Writer\StandaloneExtensionManager;
use PHPUnit\Framework\TestCase;

class StandaloneExtensionManagerTest extends TestCase
{
    private StandaloneExtensionManager $extensions;

    protected function setUp(): void
    {
        $this->extensions = new StandaloneExtensionManager();
    }

    public function testIsAnExtensionManagerImplementation(): void
    {
        $this->assertInstanceOf(ExtensionManagerInterface::class, $this->extensions);
    }

    /** @psalm-return array<string, array{0: string, 1: class-string}> */
    public static function defaultPlugins(): array
    {
        // phpcs:disable Generic.Files.LineLength.TooLong
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
            'WellFormedWeb\Renderer\Entry' => ['WellFormedWeb\Renderer\Entry', Extension\WellFormedWeb\Renderer\Entry::class],
        ];
        // phpcs:enable Generic.Files.LineLength.TooLong
    }

    /**
     * @dataProvider defaultPlugins
     */
    public function testHasAllDefaultPlugins(string $pluginName): void
    {
        $this->assertTrue($this->extensions->has($pluginName));
    }

    /**
     * @dataProvider defaultPlugins
     * @psalm-param class-string $pluginClass
     */
    public function testCanRetrieveDefaultPluginInstances(string $pluginName, string $pluginClass): void
    {
        $extension = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $extension);
    }

    /**
     * @dataProvider defaultPlugins
     * @psalm-param class-string $pluginClass
     */
    public function testEachPluginRetrievalReturnsNewInstance(string $pluginName, string $pluginClass): void
    {
        $extension = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $extension);

        $test = $this->extensions->get($pluginName);
        $this->assertInstanceOf($pluginClass, $test);
        $this->assertNotSame($extension, $test);
    }

    public function testAddAcceptsValidExtensionClasses(): void
    {
        /** @psalm-suppress UndefinedClass,ArgumentTypeCoercion */
        $this->extensions->add('Test/Feed', 'MyTestExtension_Feed');
        $this->assertTrue($this->extensions->has('Test/Feed'));

        /** @psalm-suppress UndefinedClass,ArgumentTypeCoercion */
        $this->extensions->add('Test/Entry', 'MyTestExtension_Entry');
        $this->assertTrue($this->extensions->has('Test/Entry'));

        $this->extensions->add('Test/Thing', Extension\AbstractRenderer::class);
        $this->assertTrue($this->extensions->has('Test/Thing'));
    }

    public function testAddRejectsInvalidExtensions(): void
    {
        $this->expectException(InvalidArgumentException::class);
        /** @psalm-suppress UndefinedClass,ArgumentTypeCoercion */
        $this->extensions->add('Test/Feed', 'blah');
    }

    public function testExtensionRemoval(): void
    {
        /** @psalm-suppress UndefinedClass,ArgumentTypeCoercion */
        $this->extensions->add('Test/Entry', 'MyTestExtension_Entry');
        $this->assertTrue($this->extensions->has('Test/Entry'));
        $this->extensions->remove('Test/Entry');
        $this->assertFalse($this->extensions->has('Test/Entry'));
    }
}
