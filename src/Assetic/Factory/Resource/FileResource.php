<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Resource;

/**
 * A resource is something formulae can be loaded from.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FileResource implements ResourceInterface
{
    private $path;

    /**
     * Constructor.
     *
     * @param string $path The path to a file
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    public function isFresh($timestamp)
    {
        return filemtime($this->path) <= $timestamp;
    }

    public function getContent()
    {
        return file_get_contents($this->path);
    }

    public function __toString()
    {
        return $this->path;
    }
}
