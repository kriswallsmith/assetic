<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Loads SCSS files using the PHP implementation of scss, scssphp.
 *
 * Scss files are mostly compatible, but there are slight differences.
 *
 * @link http://leafo.net/scssphp/
 *
 * @author Bart van den Burg <bart@samson-it.nl>
 */
class ScssphpFilter implements FilterInterface, HashableInterface
{
    private $compass = false;

    private $importPaths = array();

    private $compiled = array();

    public function enableCompass($enable = true)
    {
        $this->compass = (Boolean) $enable;
    }

    public function isCompassEnabled()
    {
        return $this->compass;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $asset->setContent($this->compile($asset));
    }

    private function compile(AssetInterface $asset)
    {
        if (isset($this->compiled[$asset->getSourcePath()])) {
            return $this->compiled[$asset->getSourcePath()];
        }

        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();

        $lc = new \scssc();
        if ($this->compass) {
            new \scss_compass($lc);
        }
        if ($root && $path) {
            $lc->addImportPath(dirname($root.'/'.$path));
        }
        foreach ($this->importPaths as $path) {
            $lc->addImportPath($path);
        }

        return $this->compiled[$asset->getSourcePath()] = $lc->compile($asset->getContent());
    }

    public function setImportPaths(array $paths)
    {
        $this->importPaths = $paths;
    }

    public function addImportPath($path)
    {
        $this->importPaths[] = $path;
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    public function hash(AssetInterface $asset)
    {
        return md5($asset->dump($this));
    }
}
