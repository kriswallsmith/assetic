<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2015 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Factory\AssetFactory;
use Assetic\Asset\AssetInterface;
use Assetic\Filter\DependencyExtractorInterface;
use Assetic\Util\CssUtils;

/**
 * Compiles Sass to CSS.
 *
 * @author Mikey Clarke <mikey.clarke@me.com>
 */
class SassphpFilter implements DependencyExtractorInterface
{
    private $includePaths = array();
    private $outputStyle;

    public function filterLoad(AssetInterface $asset)
    {
        $sass = new \Sass();

        $includePaths = array_merge(
            array($asset->getSourceDirectory()),
            $this->includePaths
        );
        $sass->setIncludePath(implode(':', $includePaths));

        if ($this->outputStyle) {
            $sass->setStyle($this->outputStyle);
        }

        $css = $sass->compile($asset->getContent());

        $asset->setContent($css);
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    public function setOutputStyle($outputStyle)
    {
        $this->outputStyle = $outputStyle;
    }

    public function setIncludePaths(array $paths)
    {
        $this->includePaths = $paths;
    }

    public function addIncludePath($path)
    {
        $this->includePaths[] = $path;
    }

    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        $children = array();

        $includePaths = $this->includePaths;
        if (null !== $loadPath && !in_array($loadPath, $includePaths)) {
            array_unshift($includePaths, $loadPath);
        }

        if (empty($includePaths)) {
            return $children;
        }

        foreach (CssUtils::extractImports($content) as $reference) {
            if ('.css' === substr($reference, -4)) {
                continue;
            }

            // the reference may or may not have an extension or be a partial
            if (pathinfo($reference, PATHINFO_EXTENSION)) {
                $needles = array(
                    $reference,
                    $this->partialize($reference),
                );
            } else {
                $needles = array(
                    $reference . '.scss',
                    $this->partialize($reference) . '.scss',
                );
            }

            foreach ($includePaths as $includePath) {
                foreach ($needles as $needle) {
                    if (file_exists($file = $includePath . '/' . $needle)) {
                        $child = $factory->createAsset($file, array(), array('root' => $includePath));
                        $children[] = $child;
                        $child->load();
                        $children = array_merge(
                            $children,
                            $this->getChildren($factory, $child->getContent(), $includePath)
                        );
                    }
                }
            }
        }

        return $children;
    }

    private function partialize($reference)
    {
        $parts = pathinfo($reference);

        if ('.' === $parts['dirname']) {
            $partial = '_' . $parts['filename'];
        } else {
            $partial = $parts['dirname'] . DIRECTORY_SEPARATOR . '_' . $parts['filename'];
        }

        if (isset($parts['extension'])) {
            $partial .= '.' . $parts['extension'];
        }

        return $partial;
    }
}
