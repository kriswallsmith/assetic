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
 * Filters assets through CssMin.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CssMinFilter implements FilterInterface
{
    private $baseDir;
    private $filters;
    private $plugins;

    public function __construct($baseDir = null)
    {
        $this->setBaseDir($baseDir);
        $this->filters = array();
        $this->plugins = array();
    }

    public function setBaseDir($baseDir)
    {
        $this->baseDir = rtrim($baseDir, '/');
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    public function setFilter($name, $value)
    {
        $this->filters[$name] = $value;
    }

    public function setPlugins(array $plugins)
    {
        $this->plugins = $plugins;
    }

    public function setPlugin($name, $value)
    {
        $this->plugins[$name] = $value;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $filters = $this->filters;
        $plugins = $this->plugins;

        if (isset($filters['ImportImports']) && true === $filters['ImportImports']) {
            // find the base path
            $sourceUrl = $asset->getSourceUrl();
            if (self::isAbsoluteUrl($sourceUrl) || self::isAbsolutePath($sourceUrl)) {
                $filters['ImportImports'] = array('BasePath' => dirname($sourceUrl));
            } elseif ($this->baseDir) {
                $filters['ImportImports'] = array('BasePath' => $this->baseDir);
                if ('.' != $dir = dirname($sourceUrl)) {
                    $filters['ImportImports']['BasePath'] .= '/'.$dir;
                }
            }
        }

        $asset->setContent(\CssMin::minify($asset->getContent(), $filters, $plugins));
    }

    static private function isAbsoluteUrl($url)
    {
        return false !== strpos($url, '://') || 0 === strpos($url, '//');
    }

    static private function isAbsolutePath($path)
    {
        return '/' == $path[0] || '\\' == $path[0] || (3 < strlen($path) && ctype_alpha($path[0]) && $path[1] == ':' && ('\\' == $path[2] || '/' == $path[2]));
    }
}
