<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Locator;

use Assetic\Asset\AssetCollection;
use Symfony\Component\Finder\Finder;

/**
 * Locates resource in watched folders by its relative path.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class PipelineAssetLocator extends FileAssetLocator
{
    private $paths;

    /**
     * Constructor.
     *
     * @param string $paths Paths to asset roots
     */
    public function __construct(array $paths = array())
    {
        foreach ($paths as $path) {
            $this->addPath($path);
        }
    }

    /**
     * Adds path to pipeline locator resources stack.
     *
     * @param string $path Assets root path
     */
    public function addPath($path)
    {
        $this->paths[] = rtrim($path, '/');
    }

    /**
     * Searches for a resource in watched paths.
     *
     * @param string $input   An input string
     * @param array  $options An array of options
     *
     * @return AssetInterface An asset or asset collection
     */
    public function locate($input, array $options = array())
    {
        $type = isset($options['type'])
              ? $options['type']
              : pathinfo($options['output'], PATHINFO_EXTENSION);

        if (2 == count($typeParts = explode('/', $type))) {
            switch ($typeParts[0]) {
                case 'index':
                    return $this->locateIndexAsset($input, $typeParts[1], $options);
                case 'directory':
                    return $this->locateDirectoryAssets($input, $typeParts[1], $options);
                case 'tree':
                    return $this->locateTreeAssets($input, $typeParts[1], $options);
                default:
                    return $this->locateSingleAsset($input, $typeParts[1], $options);
            }
        }

        return $this->locateSingleAsset($input, $type, $options)
            ?: $this->locateIndexAsset($input, $type, $options)
            ?: $this->locateDirectoryAssets($input, $type, $options);
    }

    protected function locateSingleAsset($resource, $type, array $options)
    {
        $subpath  = pathinfo($resource, PATHINFO_DIRNAME);
        $resource = pathinfo($resource, PATHINFO_BASENAME);

        $paths = array();
        foreach ($this->paths as $path) {
            if (is_dir($path = $path.'/'.$type.('.' != $subpath ? '/'.$subpath : ''))) {
                $paths[] = $path;
            }
        }

        if (!count($paths)) {
            return;
        }

        $files = Finder::create()->files()->depth(0)->name($resource.'*')->in($paths);

        return $this->createAssetFromFinder($files, $options);
    }

    protected function locateIndexAsset($resource, $type, array $options)
    {
        $paths = array();
        foreach ($this->paths as $path) {
            if (is_dir($path = $path.'/'.$type.'/'.$resource)) {
                $paths[] = $path;
            }
        }

        if (!count($paths)) {
            return;
        }

        $files = Finder::create()
            ->files()
            ->depth(0)
            ->name('index*')
            ->in($paths);

        return $this->createAssetFromFinder($files, $options);
    }

    protected function locateDirectoryAssets($resource, $type, array $options)
    {
        $path = null;
        foreach ($this->paths as $watchedPath) {
            if (is_dir($possiblePath = $watchedPath.'/'.$type.'/'.$resource)) {
                $path = $possiblePath;
                break;
            }
        }
        if (null === $path) {
            return;
        }

        $files = Finder::create()
            ->files()
            ->depth(0)
            ->sortByName(true)
            ->in($path);

        $asset = null;
        foreach ($files as $file) {
            $asset = $asset ?: new AssetCollection();
            $asset->add($this->createAssetFromPath((string) $file, $options));
        }

        return $asset;
    }

    protected function locateTreeAssets($resource, $type, array $options)
    {
        $path = null;
        foreach ($this->paths as $watchedPath) {
            if (is_dir($possiblePath = $watchedPath.'/'.$type.'/'.$resource)) {
                $path = $possiblePath;
                break;
            }
        }
        if (null === $path) {
            return;
        }

        $files = Finder::create()
            ->files()
            ->sortByName(true)
            ->in($path);

        $asset = new AssetCollection();
        foreach ($files as $file) {
            $asset->add($this->createAssetFromPath((string) $file, $options));
        }

        return $asset;
    }

    private function createAssetFromFinder(Finder $files, array $options)
    {
        $input = null;
        foreach ($files as $file) {
            $input = (string) $file;
            break;
        }
        if (null === $input) {
            return;
        }

        return $this->createAssetFromPath($input, $options);
    }

    private function createAssetFromPath($input, array $options)
    {
        list($root, $path, $input) = $this->prepareRootPathInput($input, $options);

        return $this->createFileAsset($input, $root, $path, $options['vars']);
    }
}
