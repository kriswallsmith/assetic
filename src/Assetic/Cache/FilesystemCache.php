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
 * A simple filesystem cache.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FilesystemCache implements CacheInterface
{
    private $dir;

    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    public function has($key)
    {
        return file_exists($this->dir.'/'.$key);
    }

    public function get($key)
    {
        return file_get_contents($this->dir.'/'.$key);
    }

    public function set($key, $value)
    {
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0777, true);
        }

        file_put_contents($this->dir.'/'.$key, $value);
    }

    public function remove($key)
    {
        unlink($this->dir.'/'.$key);
    }
}
