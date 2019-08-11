<?php namespace Assetic\Cache;

use Assetic\Contracts\Cache\CacheInterface;

/**
 * Adds expiration to a cache backend.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class ExpiringCache implements CacheInterface
{
    private $cache;
    private $lifetime;

    public function __construct(CacheInterface $cache, $lifetime)
    {
        $this->cache = $cache;
        $this->lifetime = $lifetime;
    }

    public function has($key)
    {
        if ($this->cache->has($key)) {
            if (time() < $this->cache->get($key.'.expires')) {
                return true;
            }

            $this->cache->remove($key.'.expires');
            $this->cache->remove($key);
        }

        return false;
    }

    public function get($key)
    {
        return $this->cache->get($key);
    }

    public function set($key, $value)
    {
        $this->cache->set($key.'.expires', time() + $this->lifetime);
        $this->cache->set($key, $value);
    }

    public function remove($key)
    {
        $this->cache->remove($key.'.expires');
        $this->cache->remove($key);
    }
}
