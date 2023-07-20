<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Reader;

use Laminas\Feed\Reader\Exception\InvalidArgumentException;
use Laminas\Feed\Reader\ExtensionPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\ServiceManager\Test\CommonPluginManagerTrait;
use PHPUnit\Framework\TestCase;

use function sprintf;

class ExtensionPluginManagerCompatibilityTest extends TestCase
{
    use CommonPluginManagerTrait;

    /**
     * @psalm-suppress ImplementedReturnTypeMismatch
     * @return ExtensionPluginManager
     */
    protected static function getPluginManager()
    {
        return new ExtensionPluginManager(new ServiceManager());
    }

    /** @return class-string */
    protected function getV2InvalidPluginException()
    {
        return InvalidArgumentException::class;
    }

    protected function getInstanceOf()
    {
    }

    public function testInstanceOfMatches(): void
    {
        $this->markTestSkipped(sprintf(
            'Skipping test; %s allows multiple extension types',
            ExtensionPluginManager::class
        ));
    }
}
