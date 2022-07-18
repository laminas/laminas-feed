<?php

declare(strict_types=1);

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
     * @param  string $uri
     * @return HeaderAwareResponseInterface
     */
    public function get($uri, array $headers = []);
}
