<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Core\Source\Finder;

use Assetic\Extension\Core\Source\FileSource;

class FileFinder implements FinderInterface
{
    private $basePaths;

    public function __construct(array $basePaths = array())
    {
        $this->basePaths = $basePaths;
    }

    public function findByLogicalPath($logicalPath)
    {
        foreach ($this->basePaths as $basePath) {
            if ($paths = glob($basePath.'/'.$logicalPath.'*')) {
                sort($paths);
                return new FileSource($paths[0]);
            }
        }
    }
}
