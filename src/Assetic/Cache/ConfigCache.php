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
 * A config cache stores values using var_export() and include.
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
     * @param string $key A cache key
     *
     * @return Boolean True if a file exists
     */
    public function has($key)
    {
        return file_exists($this->getPath($key));
    }

    /**
     * Writes a value to a file.
     *
     * @param string $key A cache key
     * @param mixed  $value    A value to cache
     */
    public function set($key, $value)
    {
        $path = $this->getPath($key);

        if (!is_dir($dir = dirname($path)) && false === @mkdir($dir, 0777, true)) {
            throw new \RuntimeException('Unable to create directory '.$dir);
        }

        if (false === @file_put_contents($path, sprintf("<?php\n\nreturn %s;\n", var_export($value, true)))) {
            throw new \RuntimeException('Unable to write file '.$path);
        }
    }

    /**
     * Loads and returns the value for the supplied cache key.
     *
     * @param string $key A cache key
     *
     * @return mixed The cached value
     */
    public function get($key)
    {
        $path = $this->getPath($key);

        if (!file_exists($path)) {
            throw new \RuntimeException('There is no cached value for '.$key);
        }

        return include $path;
    }

    /**
     * Returns a timestamp for when the cache was created.
     *
     * @param string $key A cache key
     *
     * @return integer A UNIX timestamp
     */
    public function getTimestamp($key)
    {
        $path = $this->getPath($key);

        if (!file_exists($path)) {
            throw new \RuntimeException('There is no cached value for '.$key);
        }

        if (false === $mtime = @filemtime($path)) {
            throw new \RuntimeException('Unable to determine file mtime for '.$path);
        }

        return $mtime;
    }

    /**
     * Returns the path where the file corresponding to the supplied cache key can be included from.
     *
     * @param string $key A cache key
     *
     * @return string A file path
     */
    private function getPath($key)
    {
        return $this->dir.'/'.$key[0].'/'.$key.'.php';
    }
}
