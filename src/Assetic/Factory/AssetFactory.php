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
use Assetic\Factory\Worker\WorkerInterface;
use Assetic\FilterManager;

/**
 * The asset factory creates asset objects.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AssetFactory
{
    private $baseDir;
    private $debug;
    private $am;
    private $fm;
    private $defaultOutput = 'assets/*';
    private $workers = array();

    /**
     * Constructor.
     *
     * @param string  $baseDir Path to the base directory for relative URLs
     * @param Boolean $debug   Filters prefixed with a "?" will be omitted in debug mode
     */
    public function __construct($baseDir, $debug = false)
    {
        $this->baseDir = rtrim($baseDir, '/').'/';
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
     * Sets the asset manager to use when creating asset references.
     *
     * @param AssetManager $am The asset manager
     */
    public function setAssetManager(AssetManager $am)
    {
        $this->am = $am;
    }

    /**
     * Sets the filter manager to use when adding filters.
     *
     * @param FilterManager $fm The filter manager
     */
    public function setFilterManager(FilterManager $fm)
    {
        $this->fm = $fm;
    }

    /**
     * Sets the default output value.
     *
     * @param string $output An output string
     */
    public function setDefaultOutput($output)
    {
        $this->defaultOutput = $output;
    }

    /**
     * Adds a factory worker.
     *
     * @param WorkerInterface $worker A worker
     */
    public function addWorker(WorkerInterface $worker)
    {
        $this->workers[] = $worker;
    }

    /**
     * Creates a new asset.
     *
     * Prefixing a filter name with a question mark will cause it to be
     * omitted when the factory is in debug mode.
     *
     * @param array   $sourceUrls  An array of URLs relative to the base directory
     * @param array   $filterNames An array of filter names
     * @param string  $output      An output string
     * @param string  $assetName   The asset name, for interpolation only
     * @param Boolean $debug       Debug mode for the asset
     *
     * @return AssetInterface An asset
     */
    public function createAsset(array $sourceUrls = array(), array $filterNames = array(), $output = null, $assetName = null, $debug = null)
    {
        if (null === $output) {
            $output = $this->defaultOutput;
        }

        if (null === $assetName) {
            $assetName = $this->generateAssetName($sourceUrls, $filterNames);
        }

        if (null === $debug) {
            $debug = $this->debug;
        }

        $asset = $this->createAssetCollection();

        // inner assets
        foreach ($sourceUrls as $sourceUrl) {
            $asset->add($this->parseAsset($sourceUrl));
        }

        // filters
        foreach ($filterNames as $filterName) {
            if ('?' != $filterName[0]) {
                $asset->ensureFilter($this->getFilter($filterName));
            } elseif (!$debug) {
                $asset->ensureFilter($this->getFilter(substr($filterName, 1)));
            }
        }

        // output --> target url
        if ($targetUrl = $this->parseOutput($output, $assetName)) {
            $asset->setTargetUrl($targetUrl);
        }

        foreach ($this->workers as $worker) {
            $worker->process($asset);
        }

        return $asset;
    }

    public function generateAssetName($sourceUrls, $filterNames)
    {
        return substr(sha1(serialize(array_merge($sourceUrls, $filterNames))), 0, 7);
    }

    /**
     * Parses an source URL string into an asset.
     *
     * The source URL string can be one of the following:
     *
     *  * A reference:     If the string starts with a "@" it will be interpreted as a reference to an asset in the asset manager
     *  * An absolute URL: If the string contains "://" it will be interpreted as a remote asset
     *  * A glob:          If the string contains a "*" it will be interpreted as a glob
     *  * A path:          Otherwise the string is interpreted as a path
     *
     * Both globs and paths will be absolutized using the current base directory.
     *
     * @param string $sourceUrl A source URL
     *
     * @return AssetInterface An asset
     */
    protected function parseAsset($sourceUrl)
    {
        if ('@' == $sourceUrl[0]) {
            return $this->createAssetReference(substr($sourceUrl, 1));
        }

        if (false !== strpos($sourceUrl, '://')) {
            return $this->createFileAsset($sourceUrl, $sourceUrl);
        }

        $baseDir = '/' == $sourceUrl[0] ? '' : $this->baseDir;
        if (false !== strpos($sourceUrl, '*')) {
            return $this->createGlobAsset($baseDir . $sourceUrl, $this->baseDir);
        } else {
            return $this->createFileAsset($baseDir . $sourceUrl, $sourceUrl);
        }
    }

    /**
     * Converts an output string to a target URL.
     *
     * An output string can be one of the following:
     *
     *  * A pattern:    If the string contains a "*" the "*" will be replaced with the provided asset name
     *  * An extension: If the string contains only letters it will be interpreted as an extension and exploded to the pattern "ext/*.ext"
     *  * An URL:       Otherwise, the output string will be returned untouched
     *
     * @param string $output    An output string
     * @param string $assetName The asset name
     *
     * @return string A target URL
     */
    protected function parseOutput($output, $assetName)
    {
        if (ctype_alpha($output)) {
            // extension
            return sprintf('%s/%s.%1$s', $output, $assetName);
        } elseif (false !== strpos($output, '*')) {
            // pattern
            return str_replace('*', $assetName, $output);
        } else {
            // simple
            return $output;
        }
    }

    protected function createAssetCollection()
    {
        return new AssetCollection();
    }

    protected function createAssetReference($name)
    {
        if (!$this->am) {
            throw new \LogicException('There is no asset manager.');
        }

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
        if (!$this->fm) {
            throw new \LogicException('There is no filter manager.');
        }

        return $this->fm->get($name);
    }
}
