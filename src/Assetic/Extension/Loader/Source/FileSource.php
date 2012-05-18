<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Loader\Source;

class FileSource implements SourceInterface
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getContent()
    {
        if (is_file($this->path)) {
            return file_get_contents($this->path);
        }
    }

    public function getLastModified()
    {
        if (is_file($this->path)) {
            return filemtime($this->path);
        }
    }

    public function getTypes()
    {
        return array_slice(explode('.', $this->path), 1);
    }
}
