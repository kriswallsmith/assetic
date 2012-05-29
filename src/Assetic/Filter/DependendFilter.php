<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * A filter which has can look for dependencies in the assets it manipulates
 *
 * @author Philipp A. Mohrenweiser <phiamo@googlemail.com>
 */
abstract class DependendFilter implements DependendFilterInterface
{
    protected $dependencyFiles = array();

    /**
     * Check if Content has a dependency
     * @param AssetInterface $asset
     */
    public function hasDependencies(AssetInterface $asset)
    {
        $path = $asset->getSourceRoot().DIRECTORY_SEPARATOR.$asset->getSourcePath();
        $plainAssetContent = file_get_contents($path);
        return preg_match('/\s*@import.*[\'|\"](.*)[\'|\"].*;\s*/iU', $plainAssetContent);
    }

    public function getDependencyLastModified(AssetInterface $asset)
    {
        //TODO: make this work with remote assets

        $path = $asset->getSourceRoot().DIRECTORY_SEPARATOR.$asset->getSourcePath();

        return $this->getLastModifiedDependencyDate($path);
    }

    protected function getLastModifiedDependencyDate($path)
    {
        $dependendModificationDate = 0;
        $this->addDependencyFiles($path);
        foreach ($this->dependencyFiles as $file) {
            $fileModificationDate = filemtime($file);
            if ($dependendModificationDate < $fileModificationDate) {
                $dependendModificationDate = $fileModificationDate;
            }
        }
        return $dependendModificationDate;
    }

    protected function addDependencyFiles($path)
    {
        $files = array();
        $return = null;
        //TODO: use asset factory to generate a new asset
        $plainAssetContent = file_get_contents($path);
        $baseDir = dirname($path);
        if ($return = preg_match_all('/\s*@import.*[\'|\"](.*)[\'|\"].*;\s*/iU', $plainAssetContent, $matches)) {
            foreach ($matches[1] as $file) {
                $filePath = $baseDir . DIRECTORY_SEPARATOR . $file;
                if (!in_array($filePath, $this->dependencyFiles)) {
                    $this->dependencyFiles[] = $filePath;
                    $this->addDependencyFiles($filePath);
                }
            }
        }
    }
}