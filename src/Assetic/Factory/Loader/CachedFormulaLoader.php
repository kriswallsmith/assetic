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
 * Adds a caching layer to a loader.
 *
 * A cached formula loader is a composition of a formula loader and a cache.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CachedFormulaLoader implements FormulaLoaderInterface
{
    private $loader;
    private $configCache;
    private $debug;

    /**
     * Constructor.
     *
     * When the loader is in debug mode it will ensure the cached formulae
     * are fresh before returning them.
     *
     * @param FormulaLoaderInterface $loader      A formula loader
     * @param ConfigCache            $configCache A config cache
     * @param Boolean                $debug       The debug mode
     */
    public function __construct(FormulaLoaderInterface $loader, ConfigCache $configCache, $debug = false)
    {
        $this->loader = $loader;
        $this->configCache = $configCache;
        $this->debug = $debug;
    }

    public function load(ResourceInterface $resource)
    {
        if (!$resource instanceof \Traversable) {
            $resource = array($resource);
        }

        $formulae = array();

        foreach ($resource as $r) {
            $cacheKey = md5(serialize($r));

            if (!$this->configCache->has($cacheKey) || ($this->debug && !$r->isFresh($this->configCache->getTimestamp($cacheKey)))) {
                $formulae += $this->loader->load($r);
                $this->configCache->set($cacheKey, $formulae);
            } else {
                $formulae += $this->configCache->get($cacheKey);
            }
        }

        return $formulae;
    }
}
