<?php namespace Assetic\Cache;

use Assetic\Contracts\Cache\CacheInterface;

/**
 * A simple array cache
 *
 * @author Michael Mifsud <xzyfer@gmail.com>
 */
class ArrayCache implements CacheInterface
{
    private $cache = [];

    /**
     * @see CacheInterface::has()
     */
    public function has($key)
    {
        return isset($this->cache[$key]);
    }

    /**
     * @see CacheInterface::get()
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new \RuntimeException('There is no cached value for '.$key);
        }

        return $this->cache[$key];
    }

    /**
     * @see CacheInterface::set()
     */
    public function set($key, $value)
    {
        $this->cache[$key] = $value;
    }

    /**
     * @see CacheInterface::remove()
     */
    public function remove($key)
    {
        unset($this->cache[$key]);
    }
}
