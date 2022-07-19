<?php

declare(strict_types=1);

namespace Laminas\Feed\Reader;

/**
 * This interface exists to provide type inference for container implementations that retrieve extensions.
 * Methods have been migrated from declared in PHP to being part of the docblock signature,
 * in order to avoid conflicting with `psr/container` signatures, which are way stricter, and
 * therefore incompatible with this one.
 *
 * @deprecated this interface is no longer needed, and shouldn't be relied upon
 *
 * @method has(string $extension): bool
 * @method get(string $extension): mixed
 */
interface ExtensionManagerInterface
{
}
