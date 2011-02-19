<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Cache;

/**
 * A config cache maintains PHP files that can be included.
 *
 * Usage looks like this:
 *
 *     $configCache->write('letters', array('a', 'b', 'c'));
 *     $letters = include $configCache->getPath('letters');
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class ConfigCache
{
    private $dir;

    /**
     * Construct.
     *
     * @param string $dir The cache directory
     */
    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    /**
     * Checks of the cache has a file.
     *
     * @param string $cacheKey A cache key
     *
     * @return Boolean True if a file exists
     */
    public function has($cacheKey)
    {
        return file_exists($this->getPath($cacheKey));
    }

    /**
     * Writes a value to a file.
     *
     * @param string $cacheKey A cache key
     * @param mixed  $value    A value to cache
     */
    public function set($cacheKey, $value)
    {
        file_put_contents($this->getPath($cacheKey), sprintf("<?php\n\nreturn %s;\n", var_export($value, true)));
    }

    /**
     * Loads and returns the value for the supplied cache key.
     *
     * @param string $cacheKey A cache key
     *
     * @return mixed The cached value
     */
    public function get($cacheKey)
    {
        return include $this->getPath($cacheKey);
    }

    /**
     * Returns a timestamp for when the cache was created.
     *
     * @param string $cacheKey A cache key
     *
     * @return integer A UNIX timestamp
     */
    public function getTimestamp($cacheKey)
    {
        return filemtime($this->getPath($cacheKey));
    }

    /**
     * Returns the path where the file corresponding to the supplied cache key can be included from.
     *
     * @param string $cacheKey A cache key
     *
     * @return string A file path
     */
    private function getPath($cacheKey)
    {
        return $this->dir.'/'.$cacheKey.'.php';
    }
}
