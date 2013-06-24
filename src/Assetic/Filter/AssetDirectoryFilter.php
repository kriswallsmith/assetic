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
use Assetic\Util\AssetDirectory;

/**
 * Parses URLs in a CSS and move all assets to a folder,
 * changing location of them in CSS.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class AssetDirectoryFilter extends BaseCssFilter
{
    /**
     * @var AssetDirectory
     */
    protected $directory;

    public function __construct(AssetDirectory $directory)
    {
        $this->directory = $directory;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $filter = $this;
        $content = $this->filterReferences($asset->getContent(), function ($match) use ($asset, $filter) {
            $url = $match['url'];

            $url = $filter->filterUrl($asset, $url);

            return 'url('.$match[1].$url.$match[3].')';
        });

        $asset->setContent($content);
    }

    public function filterUrl(AssetInterface $asset, $url)
    {
        $sourceBase = $asset->getSourceRoot();
        $sourcePath = $asset->getSourcePath();

        if (false !== strpos($sourceBase, '://')) {
            return $url;
        }


        // we need to resolve path, because fake paths are not accepted:
        // ie: path/to/inexisting/../folder
        $targetDir = $sourceBase.'/'.dirname($asset->getTargetPath());
        $file = $targetDir.'/'.$url;
        do {
            $last = $file;
            $file = preg_replace('#/[^/]+/\.\./#', '/', $last);
        } while ($last !== $file);

        // we need to remove #... and ?...
        if (false !== $pos = strpos($file, '?')) {
            $file = substr($file, 0, $pos);
        }
        if (false !== $pos = strpos($file, '#')) {
            $file = substr($file, 0, $pos);
        }

        $image = $this->directory->add($file);
        $targetPath = $sourceBase.'/'.$asset->getTargetPath();
        $sourcePath = $this->directory->getDirectory().'/'.$image;

        $path = '';
        while (0 !== strpos($sourcePath, $targetDir)) {
            if (false !== $pos = strrpos($targetDir, '/')) {
                $targetDir = substr($targetDir, 0, $pos);
                $path .= '../';
            } else {
                $targetDir = '';
                $path .= '../';
                break;
            }
        }
        $path .= ltrim(substr(dirname($sourcePath).'/', strlen($targetDir)), '/');

        return $path.$image;
    }
}
