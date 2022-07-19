<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer;

use function call_user_func_array;
use function method_exists;
use function sprintf;

/**
 * Default implementation of ExtensionManagerInterface
 *
 * Decorator for an ExtensionManagerInstance.
 */
class ExtensionManager implements ExtensionManagerInterface
{
    /** @var ExtensionManagerInterface */
    protected $pluginManager;

    /**
     * Seeds the extension manager with a plugin manager; if none provided,
     * creates and decorates an instance of StandaloneExtensionManager.
     */
    public function __construct(?ExtensionManagerInterface $pluginManager = null)
    {
        if (null === $pluginManager) {
            $pluginManager = new StandaloneExtensionManager();
        }
        $this->pluginManager = $pluginManager;
    }

    /**
     * Method overloading
     *
     * Proxy to composed ExtensionManagerInterface instance.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws Exception\BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (! method_exists($this->pluginManager, $method)) {
            throw new Exception\BadMethodCallException(sprintf(
                'Method by name of %s does not exist in %s',
                $method,
                self::class
            ));
        }
        return call_user_func_array([$this->pluginManager, $method], $args);
    }

    /**
     * Get the named extension
     *
     * @param  string $extension
     * @return Extension\AbstractRenderer
     */
    public function get($extension)
    {
        return $this->pluginManager->get($extension);
    }

    /**
     * Do we have the named extension?
     *
     * @param  string $extension
     * @return bool
     */
    public function has($extension)
    {
        return $this->pluginManager->has($extension);
    }
}
