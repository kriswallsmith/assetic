<?php

namespace Assetic;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetReference;
use Assetic\Asset\GlobAsset;
use Assetic\Asset\FileAsset;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The asset factory creates asset objects.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AssetFactory
{
    private $baseDir;
    private $am;
    private $fm;

    /**
     * Constructor.
     *
     * @param string        $baseDir Path to the base directory for relative URLs
     * @param AssetManager  $am      An asset manager
     * @param FilterManager $fm      The filter manager
     */
    public function __construct($baseDir, AssetManager $am, FilterManager $fm)
    {
        $this->baseDir = $baseDir;
        $this->am = $am;
        $this->fm = $fm;
    }

    /**
     * Creates a new asset.
     *
     * Each asset URL can take one of three forms:
     *
     *  * @jquery:      A reference to the asset manager's "jquery" asset
     *  * js/core/*:    A glob relative to the base directory
     *  * js/jquery.js: A file path relative to the base directory
     *
     * Filters can be marked as optional by prefixing the filter name with a
     * "?" character.
     *
     * If no URL for the new asset is provided, one will be automatically
     * generated. This behavior can be disabled by passing a value of false.
     *
     * If the provided URL does not include a slash or dot, it is assumed to
     * be an extension and the rest of the URL will be automatically
     * generated.
     *
     * @param array  $assetUrls   An array of URLs relative to the base directory
     * @param array  $filterNames An array of filter names
     * @param string $url         An URL for the asset
     *
     * @return AssetInterface An asset
     */
    public function createAsset($assetUrls = array(), $filterNames = array(), $url = null)
    {
        // build an array of child assets and collect extensions
        $assets = array();
        $extensions = array();
        foreach ($assetUrls as $assetUrl) {
            if ('@' == $assetUrl[0]) {
                $assets[] = $this->createAssetReference(substr($assetUrl, 1));
            } elseif (false !== strpos($assetUrl, '*')) {
                $assets[] = $this->createGlobAsset($this->baseDir . '/' . $assetUrl, $this->baseDir);
            } else {
                $assets[] = $this->createFileAsset($this->baseDir . '/' . $assetUrl, $assetUrl);
            }

            $extensions[] = pathinfo($assetUrl, PATHINFO_EXTENSION);
        }

        // build an array of filters
        $filters = array();
        foreach ($filterNames as $filterName) {
            if ('?' == $filterName[0]) {
                if ($filter = $this->getFilter(substr($filterName, 1), true)) {
                    $filters[] = $filter;
                }
            } else {
                $filters[] = $this->getFilter($filterName);
            }
        }

        if (is_string($url) && false === strpos($url, '/') && false === strpos($url, '.')) {
            // just an extension
            $extension = $url;
            $url = null;
        } elseif (null === $url) {
            // use the most common extension
            $extensions = array_filter($extensions);
            if (count($extensions)) {
                $votes = array_count_values($extensions);
                arsort($votes);
                $extensions = array_keys($votes);
                $extension = $extensions[0];
            } else {
                $extension = null;
            }
        }

        if (null === $url) {
            $parts = array_merge($assetUrls, $filterNames);
            sort($parts);
            $url = substr(sha1(serialize($parts)), 0, 7);

            if ($extension) {
                $url = $extension . '/' . $url . '.' . $extension;
            } else {
                $url = 'assets/' . $url;
            }
        }

        return $this->buildAsset($assets, $filters, $url);
    }

    protected function buildAsset($assets, $filters, $url)
    {
        if (1 == count($assets)) {
            $asset = $assets[0];
        } else {
            $asset = new AssetCollection($assets);
        }

        foreach ($filters as $filter) {
            $asset->ensureFilter($filter);
        }

        if (false !== $url) {
            $asset->setUrl($url);
        }

        return $asset;
    }

    protected function createAssetReference($name)
    {
        return new AssetReference($this->am, $name);
    }

    protected function createGlobAsset($glob, $baseDir)
    {
        return new GlobAsset($glob, $baseDir);
    }

    protected function createFileAsset($path, $url)
    {
        return new FileAsset($path, $url);
    }

    protected function getFilter($name, $optional = false)
    {
        if (!$optional || $this->fm->has($name)) {
            return $this->fm->get($name);
        }
    }
}
