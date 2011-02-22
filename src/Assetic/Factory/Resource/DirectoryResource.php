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
class DirectoryResource extends \RecursiveFilterIterator implements ResourceInterface
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

        parent::__construct(new \RecursiveDirectoryIterator($path));
    }

    public function accept()
    {
        return null === $this->pattern || 0 < preg_match($this->pattern, parent::current()->getBasename());
    }

    /**
     * Returns the current resource.
     *
     * @return ResourceInterface A resource
     */
    public function current()
    {
        $file = parent::current();

        return $this->createResource($file->getPathname());
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

    /**
     * Creates a new resource from a filesystem path.
     *
     * @param string $path A filesystem path
     *
     * @return ResourceInterface A resource
     */
    protected function createResource($path)
    {
        return new FileResource($path);
    }
}
