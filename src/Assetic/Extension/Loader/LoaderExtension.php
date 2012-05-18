<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Loader;

use Assetic\AbstractExtension;
use Assetic\Extension\Loader\Finder\ChainFinder;
use Assetic\Extension\Loader\Finder\FileFinder;
use Assetic\Extension\Loader\Loader\PathResolver;
use Assetic\Extension\Loader\Loader\SourceLoader;

/**
 * Introduces the concepts of finders and sources.
 */
class LoaderExtension extends AbstractExtension
{
    private $basePaths;
    private $finders;

    public function __construct(array $basePaths = array())
    {
        $this->basePaths = $basePaths;
        $this->finders = array();
    }

    public function getLoaderVisitors()
    {
        $finders = $this->finders;
        $finders[] = new FileFinder($this->basePaths);

        return array(
            new SourceLoader(1 == count($finders) ? $finders[0] : new ChainFinder($finders)),
            new PathResolver(),
        );
    }

    public function addFinder(FinderInterface $finder)
    {
        $this->finders[] = $finder;
    }

    public function getName()
    {
        return 'loader';
    }
}
