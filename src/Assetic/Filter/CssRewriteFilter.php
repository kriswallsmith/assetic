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
use Assetic\Util\PathUtils;

/**
 * Fixes relative CSS urls.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CssRewriteFilter extends BaseCssFilter
{
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $sourceBase = $asset->getSourceRoot();
        $sourcePath = $asset->getSourcePath();
        $targetPath = $asset->getTargetPath();

        if (null === $sourcePath || null === $targetPath || $sourcePath == $targetPath) {
            return;
        }

        // learn how to get from the target back to the source
        if (false !== strpos($sourceBase, '://')) {
            list($scheme, $url) = explode('://', $sourceBase.'/'.$sourcePath, 2);
            list($host, $path) = explode('/', $url, 2);

            $host = $scheme.'://'.$host.'/';
            $path = false === strpos($path, '/') ? '' : dirname($path);
            $path .= '/';
        } else {
            // assume source and target are on the same host
            $host = '';
            $path = PathUtils::resolveRelative($sourcePath, $targetPath);
        }

        $content = $this->filterReferences($asset->getContent(), function($matches) use ($host, $path) {
            if (!PathUtils::isPath($matches['url'])) {
                return $matches[0];
            }

            if (PathUtils::isRootPath($matches['url'])) {
                // root relative
                return str_replace($matches['url'], $host.$matches['url'], $matches[0]);
            }

            // document relative
            $url = $matches['url'];
            while (0 === strpos($url, '../') && 2 <= substr_count($path, '/')) {
                $path = substr($path, 0, strrpos(rtrim($path, '/'), '/') + 1);
                $url = substr($url, 3);
            }

            return str_replace($matches['url'], PathUtils::resolveUps($host.$path.$url), $matches[0]);
        });

        $asset->setContent($content);
    }
}
