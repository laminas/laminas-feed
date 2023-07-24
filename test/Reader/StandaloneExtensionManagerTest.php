<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Reader;

use Laminas\Feed\Reader\Exception\InvalidArgumentException;
use Laminas\Feed\Reader\Extension;
use Laminas\Feed\Reader\ExtensionManagerInterface;
use Laminas\Feed\Reader\StandaloneExtensionManager;
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

    /** @psalm-return array<string, array{0:string, 1: class-string}> */
    public static function defaultPlugins(): array
    {
        return [
            'Atom\Entry'            => ['Atom\Entry', Extension\Atom\Entry::class],
            'Atom\Feed'             => ['Atom\Feed', Extension\Atom\Feed::class],
            'Content\Entry'         => ['Content\Entry', Extension\Content\Entry::class],
            'CreativeCommons\Entry' => ['CreativeCommons\Entry', Extension\CreativeCommons\Entry::class],
            'CreativeCommons\Feed'  => ['CreativeCommons\Feed', Extension\CreativeCommons\Feed::class],
            'DublinCore\Entry'      => ['DublinCore\Entry', Extension\DublinCore\Entry::class],
            'DublinCore\Feed'       => ['DublinCore\Feed', Extension\DublinCore\Feed::class],
            'Podcast\Entry'         => ['Podcast\Entry', Extension\Podcast\Entry::class],
            'Podcast\Feed'          => ['Podcast\Feed', Extension\Podcast\Feed::class],
            'Slash\Entry'           => ['Slash\Entry', Extension\Slash\Entry::class],
            'Syndication\Feed'      => ['Syndication\Feed', Extension\Syndication\Feed::class],
            'Thread\Entry'          => ['Thread\Entry', Extension\Thread\Entry::class],
            'WellFormedWeb\Entry'   => ['WellFormedWeb\Entry', Extension\WellFormedWeb\Entry::class],
        ];
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
        $this->extensions->add('Test/Entry', Extension\AbstractEntry::class);
        $this->assertTrue($this->extensions->has('Test/Entry'));
        $this->extensions->add('Test/Feed', Extension\AbstractFeed::class);
        $this->assertTrue($this->extensions->has('Test/Feed'));
    }

    public function testAddRejectsInvalidExtensions(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->extensions->add('Test/Entry', 'blah');
    }

    public function testExtensionRemoval(): void
    {
        $this->extensions->add('Test/Entry', Extension\AbstractEntry::class);
        $this->assertTrue($this->extensions->has('Test/Entry'));
        $this->extensions->remove('Test/Entry');
        $this->assertFalse($this->extensions->has('Test/Entry'));
    }
}
