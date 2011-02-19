<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Loader;

use Assetic\Cache\ConfigCache;
use Assetic\Factory\Resource\ResourceInterface;

/**
 * Adds a caching layer between a loader and its resources.
 *
 * A cached formula loader is a composition of a formula loader and a cache.
 *
 * The loader will check if the resource needs to be reloaded when the debug
 * mode is on.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CachedFormulaLoader implements FormulaLoaderInterface
{
    private $loader;
    private $configCache;
    private $debug;

    public function __construct(FormulaLoaderInterface $loader, ConfigCache $configCache, $debug = false)
    {
        $this->loader = $loader;
        $this->configCache = $configCache;
        $this->debug = $debug;
    }

    public function supports(ResourceInterface $resource)
    {
        return $this->loader->supports($resource);
    }

    public function load(ResourceInterface $resource)
    {
        $cacheKey = md5(serialize($resource));

        if (!$this->configCache->has($cacheKey) || ($this->debug && !$resource->isFresh($this->configCache->getTimestamp($cacheKey)))) {
            $this->configCache->write($cacheKey, $this->loader->load($resource));
        }

        return include $this->configCache->getPath($cacheKey);
    }
}
