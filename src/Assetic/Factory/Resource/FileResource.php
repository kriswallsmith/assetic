<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Resource;

/**
 * A file resource.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FileResource implements ResourceInterface
{
    private $path;

    /**
     * Constructor.
     *
     * @param string $path The path to the resource
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getContent()
    {
        return file_get_contents($this->path);
    }

    public function isFresh($timestamp)
    {
        return $timestamp >= filemtime($this->path);
    }
}
