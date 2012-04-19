<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory;

use Assetic\Locator\AssetLocatorInterface;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetCollectionInterface;
use Assetic\Asset\AssetInterface;
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
    private $root;
    private $debug;
    private $output;
    private $workers;
    private $locators;
    private $am;
    private $fm;

    /**
     * Constructor.
     *
     * @param string  $root   The default root directory
     * @param string  $output The default output string
     * @param Boolean $debug  Filters prefixed with a "?" will be omitted in debug mode
     */
    public function __construct($root, $debug = false)
    {
        $this->root      = rtrim($root, '/');
        $this->debug     = $debug;
        $this->output    = 'assetic/*';
        $this->workers   = array();
        $this->locators  = array();
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
     * Checks if the factory is in debug mode.
     *
     * @return Boolean Debug mode
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * Sets the default output string.
     *
     * @param string $output The default output string
     */
    public function setDefaultOutput($output)
    {
        $this->output = $output;
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
     * Adds asset locator.
     *
     * @param AssetLocatorInterface $locator A locator
     */
    public function addLocator(AssetLocatorInterface $locator)
    {
        $this->locators[] = $locator;
    }

    /**
     * Returns the current asset manager.
     *
     * @return AssetManager|null The asset manager
     */
    public function getAssetManager()
    {
        return $this->am;
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
     * Returns the current filter manager.
     *
     * @return FilterManager|null The filter manager
     */
    public function getFilterManager()
    {
        return $this->fm;
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
     *  * root:   An array or string of more root directories
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
            $options['output'] = $this->output;
        }

        if (!isset($options['vars'])) {
            $options['vars'] = array();
        }

        if (!isset($options['debug'])) {
            $options['debug'] = $this->debug;
        }

        if (!isset($options['root'])) {
            $options['root'] = array($this->root);
        } else {
            if (!is_array($options['root'])) {
                $options['root'] = array($options['root']);
            }

            $options['root'][] = $this->root;
        }

        if (!isset($options['name'])) {
            $options['name'] = $this->generateAssetName($inputs, $filters, $options);
        }

        $asset = $this->createAssetCollection(array(), $options);
        $extensions = array();

        // inner assets
        foreach ($inputs as $input) {
            if (is_array($input)) {
                // nested formula
                $asset->add(call_user_func_array(array($this, 'createAsset'), $input));
            } else {
                $asset->add($this->locateAsset($input, $options));
                $extensions[pathinfo($input, PATHINFO_EXTENSION)] = true;
            }
        }

        // filters
        foreach ($filters as $filter) {
            if ('?' != $filter[0]) {
                $asset->ensureFilter($this->getFilter($filter));
            } elseif (!$options['debug']) {
                $asset->ensureFilter($this->getFilter(substr($filter, 1)));
            }
        }

        // append variables
        if (!empty($options['vars'])) {
            $toAdd = array();
            foreach ($options['vars'] as $var) {
                if (false !== strpos($options['output'], '{'.$var.'}')) {
                    continue;
                }

                $toAdd[] = '{'.$var.'}';
            }

            if ($toAdd) {
                $options['output'] = str_replace('*', '*.'.implode('.', $toAdd), $options['output']);
            }
        }

        // append consensus extension if missing
        if (1 == count($extensions) && !pathinfo($options['output'], PATHINFO_EXTENSION) && $extension = key($extensions)) {
            $options['output'] .= '.'.$extension;
        }

        // output --> target url
        $asset->setTargetPath(str_replace('*', $options['name'], $options['output']));

        // apply workers and return
        return $this->applyWorkers($asset);
    }

    public function generateAssetName($inputs, $filters, $options = array())
    {
        foreach (array_diff(array_keys($options), array('output', 'debug', 'root')) as $key) {
            unset($options[$key]);
        }

        ksort($options);

        return substr(sha1(serialize($inputs).serialize($filters).serialize($options)), 0, 7);
    }

    /**
     * Uses registered asset locators to create asset instance from provided input.
     *
     * @param string $input   An input string
     * @param array  $options An array of options
     *
     * @return AssetInterface An asset
     */
    protected function locateAsset($input, array $options = array())
    {
        foreach ($this->locators as $locator) {
            if ($asset = $locator->locate($input, $options)) {
                return $asset;
            }
        }

        throw new \LogicException(sprintf('Can not locate asset "%s".', $input));
    }

    protected function createAssetCollection(array $assets = array(), array $options = array())
    {
        return new AssetCollection($assets, array(), null, isset($options['vars']) ? $options['vars'] : array());
    }

    protected function getFilter($name)
    {
        if (!$this->fm) {
            throw new \LogicException('There is no filter manager.');
        }

        return $this->fm->get($name);
    }

    /**
     * Filters an asset collection through the factory workers.
     *
     * Each leaf asset will be processed first, followed by the asset
     * collection itself.
     *
     * @param AssetCollectionInterface $asset An asset collection
     */
    private function applyWorkers(AssetCollectionInterface $asset)
    {
        foreach ($asset as $leaf) {
            foreach ($this->workers as $worker) {
                $retval = $worker->process($leaf);

                if ($retval instanceof AssetInterface && $leaf !== $retval) {
                    $asset->replaceLeaf($leaf, $retval);
                }
            }
        }

        foreach ($this->workers as $worker) {
            $retval = $worker->process($asset);

            if ($retval instanceof AssetInterface) {
                $asset = $retval;
            }
        }

        return $asset instanceof AssetCollectionInterface ? $asset : $this->createAssetCollection(array($asset));
    }
}
