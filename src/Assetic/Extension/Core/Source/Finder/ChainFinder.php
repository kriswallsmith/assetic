<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Core\Source\Finder;

/**
 * Loops over many finders and returns the first source found.
 */
class ChainFinder implements FinderInterface
{
    private $finders;

    public function __construct($finders = array())
    {
        $this->finders = array();

        foreach ($finders as $finder) {
            $this->addFinder($finder);
        }
    }

    public function addFinder(FinderInterface $finder)
    {
        $this->finders[] = $finder;
    }

    public function findByLogicalPath($logicalPath)
    {
        foreach ($this->finders as $finder) {
            if ($source = $finder->findByLogicalPath($logicalPath)) {
                return $source;
            }
        }
    }
}
