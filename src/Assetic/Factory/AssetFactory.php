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
    private $defaultOutput;
    private $workers;
    private $am;
    private $fm;

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
        $this->defaultOutput = 'assets/*';
        $this->workers = array();
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
     * Creates a new asset.
     *
     * Prefixing a filter name with a question mark will cause it to be
     * omitted when the factory is in debug mode.
     *
     * Available options:
     *
     *  * output: An output string
     *  * name:   An asset name for interpolation in output patterns
     *  * debug:  Forces debug mode on or off for this asset
     *
     * @param array|string $inputs  An array of input strings
     * @param array|string $filters An array of filter names
     * @param array        $options An array of options
     *
     * @return AssetCollection An asset collection
     */
    public function createAsset($inputs = array(), $filters = array(), array $options = array())
    {
        if (!is_array($inputs)) {
            $inputs = array($inputs);
        }

        if (!is_array($filters)) {
            $filters = array($filters);
        }

        if (!isset($options['output'])) {
            $options['output'] = $this->defaultOutput;
        }

        if (!isset($options['name'])) {
            $options['name'] = $this->generateAssetName($inputs, $filters);
        }

        if (!isset($options['debug'])) {
            $options['debug'] = $this->debug;
        }

        $asset = $this->createAssetCollection();

        // inner assets
        foreach ($inputs as $input) {
            $asset->add($this->parseInput($input));
        }

        // filters
        foreach ($filters as $filter) {
            if ('?' != $filter[0]) {
                $asset->ensureFilter($this->getFilter($filter));
            } elseif (!$options['debug']) {
                $asset->ensureFilter($this->getFilter(substr($filter, 1)));
            }
        }

        // output --> target url
        $asset->setTargetUrl(str_replace('*', $options['name'], $options['output']));

        foreach ($this->workers as $worker) {
            $worker->process($asset, $options['debug']);
        }

        return $asset;
    }

    public function generateAssetName($inputs, $filters)
    {
        return substr(sha1(serialize(array_merge($inputs, $filters))), 0, 7);
    }

    /**
     * Parses an input string string into an asset.
     *
     * The input string can be one of the following:
     *
     *  * A reference:     If the string starts with a "@" it will be interpreted as a reference to an asset in the asset manager
     *  * An absolute URL: If the string contains "://" it will be interpreted as a remote asset
     *  * A glob:          If the string contains a "*" it will be interpreted as a glob
     *  * A path:          Otherwise the string is interpreted as a path
     *
     * Both globs and paths will be absolutized using the current base directory.
     *
     * @param string $input An input string
     *
     * @return AssetInterface An asset
     */
    protected function parseInput($input)
    {
        if ('@' == $input[0]) {
            return $this->createAssetReference(substr($input, 1));
        }

        if (false !== strpos($input, '://')) {
            return $this->createFileAsset($input, $input);
        }

        // todo: a better isAbsolutePath()
        $baseDir = '/' == $input[0] ? '' : $this->baseDir;

        if (false !== strpos($input, '*')) {
            return $this->createGlobAsset($baseDir . $input, $this->baseDir);
        } else {
            return $this->createFileAsset($baseDir . $input, $input);
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
        return new GlobAsset($glob, array(), $baseDir);
    }

    protected function createFileAsset($path, $sourceUrl = null)
    {
        return new FileAsset($path, array(), $sourceUrl);
    }

    protected function getFilter($name)
    {
        if (!$this->fm) {
            throw new \LogicException('There is no filter manager.');
        }

        return $this->fm->get($name);
    }
}
