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
use Assetic\EnvironmentInterface;
use Assetic\Extension\Loader\Finder\ChainFinder;
use Assetic\Extension\Loader\Finder\FileFinder;
use Assetic\Extension\Loader\Loader\PathResolver;
use Assetic\Extension\Loader\Loader\SourceLoader;

/**
 * Introduces the concepts of finders and sources.
 */
class LoaderExtension extends AbstractExtension implements LoaderExtensionInterface
{
    private $basePaths;
    private $finders;

    public function __construct(array $basePaths = array())
    {
        $this->basePaths = $basePaths;
        $this->finders = array();
    }

    public function initialize(EnvironmentInterface $env)
    {
        $this->finders = array();

        foreach ($env->getExtensions() as $extension) {
            if ($extension instanceof LoaderExtensionInterface) {
                $this->finders = array_merge($this->finders, $extension->getFinders());
            }
        }
    }

    public function getLoaderVisitors()
    {
        $finder = 1 == count($this->finders) ? $this->finders[0] : new ChainFinder($this->finders);

        return array(
            new SourceLoader($finder),
            new PathResolver(),
        );
    }

    public function getFinders()
    {
        return array(
            new FileFinder($this->basePaths),
        );
    }
}
