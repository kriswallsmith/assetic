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

        if (preg_match_all('/\s*@import.*[\'|\"](.*)[\'|\"].*;\s*/iU', $content, $matches)) {
            foreach ($matches[1] as $file) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                if (!$extension) {
                    $extension = "less";
                    $file .= ".$extension";
                }

                if ("less" !== $extension) {
                    continue;
                }

                $file = realpath($sourceRoot.'/'.$file);

                $imports[] = $file;
            }
        }

        foreach ($imports as $file) {
            $imports = array_merge($imports, $this->findImports(dirname($file), file_get_contents($file)));
        }

        return array_unique($imports);
    }
}
