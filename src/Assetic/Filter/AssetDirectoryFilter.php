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
use Assetic\Util\PathUtils;

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

    /**
     * @param AssetDirectory $directory directory where to store assets
     */
    public function __construct(AssetDirectory $directory)
    {
        $this->directory = $directory;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        $directory = $this->directory;

        if (null === $asset->getSourcePath() || null === $asset->getTargetPath()) {
            return;
        }

        $content = $this->filterReferences($asset->getContent(), function ($matches) use ($asset, $directory) {
            $url = $matches['url'];

            if (!PathUtils::isPath($url)) {
                return $matches[0];
            }

            $file = PathUtils::resolveUrl($asset, $url);
            $target = $this->directory->add($file);

            $path = PathUtils::resolveRelative($target, $asset->getTargetPath()).basename($file);

            return str_replace($matches['url'], $path, $matches[0]);
        });

        $asset->setContent($content);
    }
}
