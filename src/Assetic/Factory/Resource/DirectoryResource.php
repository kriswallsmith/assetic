<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Resource;

use Assetic\Factory\Resource\Loader\DirectoryLoader;

/**
 * A resource is something formulae can be loaded from.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @deprecated Deprecated since Assetic 1.2. Use the
 *             {@link Loader\DirectoryLoader} instead.
 */
class DirectoryResource implements IteratorResourceInterface
{
    private $path;
    private $pattern;

    /**
     * Constructor.
     *
     * @param string $path    A directory path
     * @param string $pattern A filename pattern
     */
    public function __construct($path, $pattern = null)
    {
        if (DIRECTORY_SEPARATOR != substr($path, -1)) {
            $path .= DIRECTORY_SEPARATOR;
        }

        $this->path = $path;
        $this->pattern = $pattern;
    }

    public function isFresh($timestamp)
    {
        if (!is_dir($this->path) || filemtime($this->path) > $timestamp) {
            return false;
        }

        foreach ($this as $resource) {
            if (!$resource->isFresh($timestamp)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the combined content of all inner resources.
     */
    public function getContent()
    {
        $content = array();
        foreach ($this as $resource) {
            $content[] = $resource->getContent();
        }

        return implode("\n", $content);
    }

    public function __toString()
    {
        return $this->path;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function getIterator()
    {
        try {
            $loader = new DirectoryLoader();

            return new \ArrayIterator($loader->load($this->path, $this->pattern));
        } catch (\InvalidArgumentException $exception) {
            // Ignore non-existing directories
            return new \EmptyIterator();
        }
    }
}
