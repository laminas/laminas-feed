<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Writer;

/**
 * Default implementation of ExtensionManagerInterface
 *
 * Decorator for an ExtensionManagerInstance.
 */
class ExtensionManager implements ExtensionManagerInterface
{
    protected $pluginManager;

    /**
     * Seeds the extension manager with a plugin manager; if none provided,
     * creates and decorates an instance of StandaloneExtensionManager.
     */
    public function __construct(ExtensionManagerInterface $pluginManager = null)
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
                __CLASS__
            ));
        }
        return call_user_func_array([$this->pluginManager, $method], $args);
    }

    /**
     * Get the named extension
     *
     * @param  string $name
     * @return Extension\AbstractRenderer
     */
    public function get($name)
    {
        return $this->pluginManager->get($name);
    }

    /**
     * Do we have the named extension?
     *
     * @param  string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->pluginManager->has($name);
    }
}
