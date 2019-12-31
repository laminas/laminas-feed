<?php

/**
 * @see       https://github.com/laminas/laminas-feed for the canonical source repository
 * @copyright https://github.com/laminas/laminas-feed/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-feed/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Feed\Reader\Http;

interface HeaderAwareClientInterface extends ClientInterface
{
    /**
     * Allow specifying headers to use when fetching a feed.
     *
     * Headers MUST be in the format:
     *
     * <code>
     * [
     *     'header-name' => [
     *         'header',
     *         'values'
     *     ]
     * ]
     * </code>
     *
     * @param string $uri
     * @param array $headers
     * @return HeaderAwareResponseInterface
     */
    public function get($uri, array $headers = []);
}
