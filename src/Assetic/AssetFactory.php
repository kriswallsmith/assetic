<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetReference;
use Assetic\Asset\GlobAsset;
use Assetic\Asset\FileAsset;

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
        $this->baseDir = $baseDir;
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
     * Each source URL can take one of three forms:
     *
     *  * @jquery:       A reference to the asset manager's "jquery" asset
     *  * js/core/*:     A glob relative to the base directory
     *  * js/jquery.js:  A file path relative to the base directory
     *  * http://etc...: An absolute URL
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
     * A multi-dimensional collection can be built by passing in an array
     * source URL value that includes more source URLs and filter names:
     *
     *     $factory->createAsset(
     *         array('css/main.css', array(array('css/more.sass'), array('sass'))),
     *         array('yui_css')
     *     );
     *
     * @param array $sourceUrls  An array of URLs relative to the base directory
     * @param array $filterNames An array of filter names
     *
     * @return AssetInterface An asset
     */
    public function createAsset(array $sourceUrls = array(), array $filterNames = array())
    {
        $asset = $this->createAssetCollection();

        // add assets
        foreach ($sourceUrls as $sourceUrl) {
            if (is_array($sourceUrl)) {
                $asset->add($this->createAsset($sourceUrl[0], $sourceUrl[1]));
            } elseif ('@' == $sourceUrl[0]) {
                $asset->add($this->createAssetReference(substr($sourceUrl, 1)));
            } elseif (false !== strpos($sourceUrl, '*')) {
                $asset->add($this->createGlobAsset($this->baseDir . '/' . $sourceUrl, $this->baseDir));
            } else {
                $asset->add($this->createFileAsset($this->baseDir . '/' . $sourceUrl, $sourceUrl));
            }
        }

        // ensure filters
        foreach ($filterNames as $filterName) {
            if ('?' != $filterName[0]) {
                $asset->ensureFilter($this->getFilter($filterName));
            } elseif (!$this->debug) {
                $asset->ensureFilter($this->getFilter(substr($filterName, 1)));
            }
        }

        return $asset;
    }

    protected function createAssetCollection()
    {
        return new AssetCollection();
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

    protected function getFilter($name)
    {
        return $this->fm->get($name);
    }
}
