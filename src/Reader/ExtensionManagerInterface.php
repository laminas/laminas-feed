<?php

namespace Laminas\Feed\Reader;

interface ExtensionManagerInterface
{
    /**
     * Do we have the extension?
     *
     * @param  string $extension
     * @return bool
     */
    public function has($extension);

    /**
     * Retrieve the extension
     *
     * @param  string $extension
     * @return mixed
     */
    public function get($extension);
}
