<?php

namespace LaminasTest\Feed\Writer;

use Laminas\Feed\Writer\Exception\InvalidArgumentException;
use Laminas\Feed\Writer\ExtensionPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\ServiceManager\Test\CommonPluginManagerTrait;
use PHPUnit\Framework\TestCase;

class ExtensionPluginManagerCompatibilityTest extends TestCase
{
    use CommonPluginManagerTrait;

    protected function getPluginManager()
    {
        return new ExtensionPluginManager(new ServiceManager());
    }

    protected function getV2InvalidPluginException()
    {
        return InvalidArgumentException::class;
    }

    protected function getInstanceOf(): void
    {
        return;
    }

    public function testInstanceOfMatches(): void
    {
        $this->markTestSkipped(sprintf(
            'Skipping test; %s allows multiple extension types',
            ExtensionPluginManager::class
        ));
    }
}
