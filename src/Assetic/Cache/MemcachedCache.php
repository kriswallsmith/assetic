<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Cache;

/**
 * Uses pecl/memcached to cache files in memcached
 *
 * @author Christer Edvartsen <cogo@starzinger.net>
 */
class MemcachedCache implements CacheInterface
{
    /**
     * @var int
     */
    public $ttl = 0;

    /**
     * @var Memcached
     */
    private $memcached;

    /**
     * Class constructor
     *
     * @param Memcached $memcached Instance of pecl/memcached
     */
    public function __construct(\Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

    /**
     * @see CacheInterface::has()
     */
    public function has($key)
    {
        return false !== $this->memcached->get($key);
    }

    /**
     * @see CacheInterface::get()
     */
    public function get($key)
    {
        $value = $this->memcached->get($key);

        if (!$value) {
            $value = null;
        }

        return $value;
    }

    /**
     * @see CacheInterface::set()
     */
    public function set($key, $value)
    {
        return $this->memcached->set($key, $value, $this->ttl);
    }

    /**
     * @see CacheInterface::remove()
     */
    public function remove($key)
    {
        return $this->memcached->delete($key);
    }
}
