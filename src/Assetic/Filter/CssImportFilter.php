<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Inlines imported stylesheets.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CssImportFilter extends BaseCssFilter
{
    public function filterLoad(AssetInterface $asset)
    {
        $filter = $this;
        $callback = function($matches) use($asset, $filter, &$callback)
        {
            if (false !== strpos($matches['url'], '://') || 0 === strpos($matches['url'], '//')) {
                // absolute or protocol-relative
                return $matches[0];
            }

            $root = $asset->getSourceRoot();
            $path = $asset->getSourcePath();

            if (null === $root) {
                // not enough information
                return $matches[0];
            }

            if ('/' == $matches['url'][0]) {
                $file = $root.$matches['url'];
            } elseif (null !== $path) {
                $file = $root.'/'.dirname($path).'/'.$matches['url'];
            } else {
                // not enough information
                return $matches[0];
            }

            if (!file_exists($file) || false === $import = @file_get_contents($file)) {
                // not found
                return $matches[0];
            }

            return $import;
        };

        $content = $this->filterImports($asset->getContent(), $callback);

        $asset->setContent($content);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
