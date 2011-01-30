<?php

namespace Assetic\Asset;

use Assetic\Filter\FilterInterface;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A collection of assets loaded by glob.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class GlobAsset extends AssetCollection
{
    private $globs;
    private $baseDir;
    private $initialized;

    /**
     * Constructor.
     *
     * @param string|array $globs   A single glob path or array of paths
     * @param string       $baseDir A base directory to use for determining each URL
     * @param array        $filters An array of filters
     *
     * @throws InvalidArgumentException If the base directory doesn't exist
     */
    public function __construct($globs, $baseDir = null, $filters = array())
    {
        $this->globs = (array) $globs;
        $this->baseDir = $baseDir;

        $this->initialized = false;

        parent::__construct(array(), $filters);
    }

    /**
     * Initializes the collection based on the glob(s) passed in.
     */
    private function initialize()
    {
        $baseDir = $this->baseDir;
        if (null !== $baseDir && false === $baseDir = realpath($baseDir)) {
            throw new \InvalidArgumentException('Invalid base directory.');
        }

        foreach ($this->globs as $glob) {
            if (false !== $paths = glob($glob)) {
                foreach (array_map('realpath', $paths) as $path) {
                    $asset = new FileAsset($path);

                    // determine url based on the base filesystem directory
                    if (null !== $baseDir && 0 === strpos($path, $baseDir)) {
                        $asset->setUrl(substr($path, strlen($baseDir) + 1));
                    }

                    $this->add($asset);
                }
            }
        }

        $this->initialized = true;
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        parent::load($additionalFilter);
    }

    public function dump(FilterInterface $additionalFilter = null)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::dump($additionalFilter);
    }

    public function getLastModified()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::getLastModified();
    }

    public function current()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::current();
    }

    public function key()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::key();
    }

    public function next()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::next();
    }

    public function rewind()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::rewind();
    }

    public function valid()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::valid();
    }

    public function getChildren()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::getChildren();
    }

    public function hasChildren()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::hasChildren();
    }
}
