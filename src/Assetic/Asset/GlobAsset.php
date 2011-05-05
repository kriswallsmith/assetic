<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset;

use Assetic\Filter\FilterInterface;

/**
 * A collection of assets loaded by glob.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class GlobAsset extends AssetCollection
{
    private $globs;
    private $initialized;

    /**
     * Constructor.
     *
     * @param string|array $globs   A single glob path or array of paths
     * @param array        $filters An array of filters
     */
    public function __construct($globs, $filters = array())
    {
        $this->globs = (array) $globs;
        $this->initialized = false;

        parent::__construct(array(), $filters);
    }

    /**
     * Initializes the collection based on the glob(s) passed in.
     */
    private function initialize()
    {
        foreach ($this->globs as $glob) {
            if (false !== $paths = glob($glob)) {
                foreach ($paths as $path) {
                    $this->add(new FileAsset($path));
                }
            }
        }

        $this->initialized = true;
    }

    public function all()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::all();
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

    public function getIterator()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return parent::getIterator();
    }
}
