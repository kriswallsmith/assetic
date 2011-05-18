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
use Assetic\Asset\FileAsset;

/**
 * Inlines imported stylesheets.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CssImportFilter extends BaseCssFilter
{
    private $debug;
    private $rewriteFilter;

    public function __construct($debug = false, FilterInterface $rewriteFilter = null)
    {
        $this->debug = $debug;
        $this->rewriteFilter = $rewriteFilter ?: new CssRewriteFilter();
    }

    public function filterLoad(AssetInterface $asset)
    {
        $debug = $this->debug;
        $rewriteFilter = $this->rewriteFilter;
        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();
        $target = $asset->getTargetPath();

        $callback = function($matches) use($debug, $rewriteFilter, $root, $path, $target)
        {
            if (!$matches['url']) {
                // empty (wtf)
                return $debug ? "/* unable to import -- empty url */\n{$matches[0]}" : $matches[0];
            }

            if (null === $root) {
                // not enough information
                return $debug ? "/* unable to import -- asset has no source root */\n{$matches[0]}" : $matches[0];
            }

            if ('/' == $matches['url'][0]) {
                $importPath = substr($matches['url'], 1);
            } elseif (null !== $path) {
                $importPath = dirname($path).'/'.$matches['url'];
            } else {
                // not enough information
                return $debug ? "/* unable to import -- asset has no source path */\n{$matches[0]}" : $matches[0];
            }

            if (!file_exists($file = $root.'/'.$importPath)) {
                // not found
                return $debug ? "/* unable to import -- file not found */\n{$matches[0]}" : $matches[0];
            }

            $import = new FileAsset($file, array($rewriteFilter), $root, $importPath);
            $import->setTargetPath($target);

            if (!$debug) {
                return $import->dump();
            }

            // add a comment about the import
            return sprintf("/* begin import: \"%s\" from \"%s\" */\n%s\n/* end import: \"%1\$s\" from \"%2\$s\" */\n", $matches['url'], $path, $import->dump());
        };

        $content = $asset->getContent();

        do {
            $count = 0;
            $content = $this->filterImports($content, $callback);
        } while (0 < $count);

        $asset->setContent($content);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
