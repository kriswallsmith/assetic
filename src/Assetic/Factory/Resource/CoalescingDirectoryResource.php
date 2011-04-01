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
 * Coalesces multiple directories together into one merged resource.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CoalescingDirectoryResource implements IteratorResourceInterface
{
    private $directories;

    public function __construct($directories)
    {
        $this->directories = array();

        foreach ($directories as $directory) {
            $this->addDirectory($directory);
        }
    }

    public function addDirectory(DirectoryResource $directory)
    {
        $this->directories[] = $directory;
    }

    public function isFresh($timestamp)
    {
        foreach ($this->getFileResources() as $file) {
            if (!$file->isFresh($timestamp)) {
                return false;
            }
        }

        return true;
    }

    public function getContent()
    {
        $parts = array();
        foreach ($this->getFileResources() as $file) {
            $parts[] = $file->getContent();
        }

        return implode("\n", $parts);
    }

    /**
     * Returns a string to uniquely identify the current resource.
     *
     * @return string An identifying string
     */
    public function __toString()
    {
        $parts = array();
        foreach ($this->directories as $directory) {
            $parts[] = (string) $directory;
        }

        return implode(',', $parts);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getFileResources());
    }

    /**
     * Performs the coalesce.
     *
     * @return array An array of file resources
     */
    private function getFileResources()
    {
        $paths = array();

        foreach ($this->directories as $directory) {
            $path = (string) $directory;
            $offset = strlen($path);
            foreach ($directory as $file) {
                $pathname = (string) $file;
                $relative = substr($pathname, $offset);
                if (!isset($paths[$relative])) {
                    $paths[$relative] = $file;
                }
            }
        }

        return array_values($paths);
    }
}
