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
 * Loads LESS files.
 *
 * @link http://lesscss.org/
 */
abstract class AbstractLessFilter implements FilterInterface
{

    /**
     * Load Paths
     *
     * A list of paths which less will search for includes.
     * 
     * @var array
     */
    protected $loadPaths = array();

    /**
     * Adds a path where less will search for includes
     * 
     * @param string $path Load path (absolute)
     */
    public function addLoadPath($path)
    {
        $this->loadPaths[] = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        $root = $asset->getSourceRoot();

        foreach ($this->findImports($root, $asset->getContent()) as $file) {
            $asset->addResourcePath($file);
        }
    }

    protected function findImports($sourceRoot, $content)
    {
        $imports = array();

        if (preg_match_all('/\s*@import\s*(\'([^\']*)\'|\"([^\"]*)\")\s*;\s*/iU', $content, $matches)) {
            foreach (array_merge($matches[2], $matches[3]) as $file) {

                $fileName = trim($file);
                if (!$fileName) {
                    continue;
                }

                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                if (!$extension) {
                    $extension = "less";
                    $fileName .= ".$extension";
                }

                if ("less" !== $extension) {
                    continue;
                }

                $file = realpath($sourceRoot.'/'.$fileName);

                foreach ($this->loadPaths as $path) {
                    if (file_exists($file)) {
                        break;
                    }

                    $file = realpath($path.'/'.$fileName);
                }

                $imports[] = $file;
            }
        }

        foreach ($imports as $file) {
            $imports = array_merge($imports, $this->findImports(dirname($file), file_get_contents($file)));
        }

        return array_unique($imports);
    }
}
