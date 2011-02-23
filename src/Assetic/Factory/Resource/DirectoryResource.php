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
 * A resource is something formulae can be loaded from.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class DirectoryResource implements ResourceInterface, \IteratorAggregate
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
        $this->path = $path;
        $this->pattern = $pattern;
    }

    public function isFresh($timestamp)
    {
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

    public function getIterator()
    {
        return new RecursiveIteratorIterator($this->getInnerIterator());
    }

    protected function getInnerIterator()
    {
        $iterator = new \RecursiveDirectoryIterator($this->path);

        if (null !== $this->pattern) {
            $iterator = new RecursiveFilterIterator($iterator, $this->pattern);
        }

        return $iterator;
    }
}

/**
 * An iterator that converts file objects into file resources.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @access private
 */
class RecursiveIteratorIterator extends \RecursiveIteratorIterator
{
    public function current()
    {
        return new FileResource(parent::current()->getPathname());
    }
}

/**
 * Filters files by a basename pattern.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @access private
 */
class RecursiveFilterIterator extends \RecursiveFilterIterator
{
    protected $pattern;

    public function __construct(\RecursiveDirectoryIterator $iterator, $pattern)
    {
        $this->pattern = $pattern;

        parent::__construct($iterator);
    }

    public function accept()
    {
        $file = $this->current();

        return $file->isDir() || 0 < preg_match($this->pattern, $file->getBasename());
    }

    public function getChildren()
    {
        return new self(new \RecursiveDirectoryIterator($this->current()->getPathname()), $this->pattern);
    }
}
