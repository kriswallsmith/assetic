<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetReference;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\AssetManager;
use Assetic\FilterManager;

/**
 * The asset factory creates asset objects.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class Factory
{
    private $baseDir;
    private $am;
    private $fm;
    private $debug;

    /**
     * Constructor.
     *
     * @param string        $baseDir Path to the base directory for relative URLs
     * @param AssetManager  $am      An asset manager
     * @param FilterManager $fm      The filter manager
     * @param Boolean       $debug   Filters prefixed with a "?" will be omitted in debug mode
     */
    public function __construct($baseDir, AssetManager $am, FilterManager $fm, $debug = false)
    {
        $this->baseDir = rtrim($baseDir, '/').'/';
        $this->am = $am;
        $this->fm = $fm;
        $this->debug = $debug;
    }

    /**
     * Sets debug mode for the current factory.
     *
     * @param Boolean $debug Debug mode
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * Creates a new asset.
     *
     * Each source URL can take one of the following forms:
     *
     *  * @jquery:         A reference to the asset manager's "jquery" asset
     *  * js/core/*:       A glob relative to the base directory
     *  * js/jquery.js:    A file path relative to the base directory
     *  * /path/to/foo.js: An absolute filesytem path
     *  * http://etc...:   An absolute URL
     *
     * Prefixing a filter name with a question mark will cause it to be
     * omitted when the factory is in debug mode.
     *
     * For example, the following asset will always go through the SASS filter
     * but only be compressed by YUI when not in debug mode:
     *
     *     $factory->createAsset(
     *         array('css/main.sass'),
     *         array('sass', '?yui_css')
     *     );
     *
     * @param array   $sourceUrls  An array of URLs relative to the base directory
     * @param array   $filterNames An array of filter names
     * @param string  $targetUrl   A target URL for the asset
     * @param string  $assetName   The asset name, for interpolation only
     * @param Boolean $debug       Debug mode for the asset
     *
     * @return AssetInterface An asset
     */
    public function createAsset(array $sourceUrls = array(), array $filterNames = array(), $targetUrl = null, $assetName = null, $debug = null)
    {
        if (null === $debug) {
            $debug = $this->debug;
        }

        $asset = $this->createAssetCollection();

        // inner assets
        foreach ($sourceUrls as $sourceUrl) {
            if ('@' == $sourceUrl[0]) {
                $asset->add($this->createAssetReference(substr($sourceUrl, 1)));
                continue;
            }

            if (false !== strpos($sourceUrl, '://')) {
                $asset->add($this->createFileAsset($sourceUrl));
                continue;
            }

            $baseDir = '/' == $sourceUrl[0] ? '' : $this->baseDir;
            if (false !== strpos($sourceUrl, '*')) {
                $asset->add($this->createGlobAsset($baseDir . $sourceUrl, $this->baseDir));
            } else {
                $asset->add($this->createFileAsset($baseDir . $sourceUrl, $sourceUrl));
            }
        }

        // filters
        foreach ($filterNames as $filterName) {
            if ('?' != $filterName[0]) {
                $asset->ensureFilter($this->getFilter($filterName));
            } elseif (!$debug) {
                $asset->ensureFilter($this->getFilter(substr($filterName, 1)));
            }
        }

        // target url
        if (false !== strpos($targetUrl, '*')) {
            // pattern
            $asset->setTargetUrl(str_replace('*', $assetName ?: $this->generateAssetName($sourceUrls, $filterNames), $targetUrl));
        } elseif (ctype_alpha($targetUrl)) {
            // extension
            $asset->setTargetUrl(sprintf('%s/%s.%1$s', $targetUrl, $assetName ?: $this->generateAssetName($sourceUrls, $filterNames)));
        } elseif ($targetUrl) {
            // simple
            $asset->setTargetUrl($targetUrl);
        } elseif (!$asset->getTargetUrl()) {
            // generate
            $asset->setTargetUrl('assets/'.$assetName ?: $this->generateAssetName($sourceUrls, $filterNames));
        }

        return $asset;
    }

    public function generateAssetName($sourceUrls, $filterNames)
    {
        return substr(sha1(serialize(array_merge($sourceUrls, $filterNames))), 0, 7);
    }

    protected function createAssetCollection()
    {
        return new AssetCollection();
    }

    protected function createAssetReference($name)
    {
        return new AssetReference($this->am, $name);
    }

    protected function createGlobAsset($glob, $baseDir = null)
    {
        return new GlobAsset($glob, $baseDir);
    }

    protected function createFileAsset($path, $sourceUrl = null)
    {
        return new FileAsset($path, $sourceUrl);
    }

    protected function getFilter($name)
    {
        return $this->fm->get($name);
    }
}
