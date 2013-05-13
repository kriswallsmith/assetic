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
class ScssphpFilter extends BaseFilter implements DependencyExtractorInterface
{
    private $compassEnabled = false;

    private $importPaths = array();

    public function setCompassEnabled($compassEnabled)
    {
        $this->compassEnabled = $compassEnabled;
    }

    public function getCompassEnabled()
    {
        return $this->compassEnabled;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();

        $lc = new \scssc();
        if ($this->compassEnabled) {
            new \scss_compass($lc);
        }
        if ($root && $path) {
            $lc->addImportPath(dirname($root.'/'.$path));
        }
        foreach ($this->getImportPaths() as $path) {
            $lc->addImportPath($path);
        }

        $asset->setContent($lc->compile($asset->getContent()));
    }

    public function getImportPaths()
    {
        return $this->importPaths;
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
        // todo
        return array();
    }
}
