<?php

declare(strict_types=1);

namespace LaminasTest\Feed\Writer\TestAsset;

use Laminas\Feed\Writer\Exception\InvalidArgumentException;
use Laminas\Feed\Writer\Extension;
use Laminas\Feed\Writer\ExtensionManagerInterface;

use function array_key_exists;
use function sprintf;

class CustomExtensionManager implements ExtensionManagerInterface
{
    /** @var array<string, class-string> */
    private array $extensions = [
        'Atom\Renderer\Feed'           => Extension\Atom\Renderer\Feed::class,
        'Content\Renderer\Entry'       => Extension\Content\Renderer\Entry::class,
        'DublinCore\Renderer\Entry'    => Extension\DublinCore\Renderer\Entry::class,
        'DublinCore\Renderer\Feed'     => Extension\DublinCore\Renderer\Feed::class,
        'ITunes\Entry'                 => Extension\ITunes\Entry::class,
        'ITunes\Feed'                  => Extension\ITunes\Feed::class,
        'ITunes\Renderer\Entry'        => Extension\ITunes\Renderer\Entry::class,
        'ITunes\Renderer\Feed'         => Extension\ITunes\Renderer\Feed::class,
        'Slash\Renderer\Entry'         => Extension\Slash\Renderer\Entry::class,
        'Threading\Renderer\Entry'     => Extension\Threading\Renderer\Entry::class,
        'WellFormedWeb\Renderer\Entry' => Extension\WellFormedWeb\Renderer\Entry::class,
    ];

    /**
     * Do we have the extension?
     *
     * @param  string $extension
     * @return bool
     */
    public function has($extension)
    {
        return array_key_exists($extension, $this->extensions);
    }

    /**
     * Retrieve the extension
     *
     * @param  string $extension
     * @return mixed
     */
    public function get($extension)
    {
        $class = $this->has($extension) ? $this->extensions[$extension] : false;
        if (! $class) {
            throw new InvalidArgumentException(sprintf(
                'Cannot fetch extension "%s"; does not exist',
                $extension
            ));
        }
        return new $class();
    }
}
