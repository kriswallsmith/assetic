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
use Assetic\Factory\AssetFactory;

/**
 * Loads SCSS files using the PHP implementation of scss, scssphp.
 *
 * Scss files are mostly compatible, but there are slight differences.
 *
 * @link http://leafo.net/scssphp/
 *
 * @author Bart van den Burg <bart@samson-it.nl>
 */
class ScssphpFilter implements DependencyExtractorInterface
{
    private $compass = false;

    private $importPaths = array();

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
        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();

        $sc = new \scssc();
        if ($this->compass) {
            new \scss_compass($sc);
        }
        if ($root && $path) {
            $sc->addImportPath(dirname($root.'/'.$path));
        }
        foreach ($this->importPaths as $path) {
            $sc->addImportPath($path);
        }

        $asset->setContent($sc->compile($asset->getContent()));
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

    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        preg_match_all('/@import "(.*)";/', $content, $m);

        $sc = new \scssc();
        $sc->addImportPath($loadPath);
        foreach($this->importPaths as $path) {
            $sc->addImportPath($path);
        }

        $children = array();
        foreach($m[1] as $match) {
            $file = $sc->findImport($match);
            if ($file) {
                $children[] = $factory->createAsset($file, array(), array('root' => $loadPath));
            }
        }

        return $children;
    }
}
