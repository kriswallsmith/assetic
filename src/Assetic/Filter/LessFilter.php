<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Filter\DependencyExtractorInterface;
use Assetic\Exception\FilterException;
use Assetic\Factory\AssetFactory;
use Assetic\Util\FilesystemUtils;
use Assetic\Util\LessUtils;

/**
 * Loads LESS files.
 *
 * @link http://lesscss.org/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class LessFilter extends BaseNodeFilter implements DependencyExtractorInterface
{
    /**
     * @var string Path to the binary for this process based filter
     */
    protected $binaryPath = '/usr/bin/lessc';

    /*
     * Filter Options
     */

    /**
     * @var boolean
     */
    private $compress;

    /**
     * @var array
     */
    private $parserOptions;

    /**
     * @var array List of paths which less will search for includes.
     */
    protected $loadPaths = [];

    /**
     * @param bool $compress
     */
    public function setCompress($compress)
    {
        $this->compress = $compress;
    }

    public function setLoadPaths(array $loadPaths)
    {
        $this->loadPaths = $loadPaths;
    }

    /**
     * Adds a path where less will search for includes
     *
     * @param string $path Load path (absolute)
     */
    public function addLoadPath($path)
    {
        $this->loadPaths[] = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        $args = $paths = [];

        if (null !== $this->compress && $this->compress) {
            $args[] = '--compress';
        }

        if ($dir = $asset->getSourceDirectory()) {
            $paths[] = $dir;
        }

        foreach ($this->loadPaths as $loadPath) {
            $paths[] = $loadPath;
        }

        if ($paths) {
            $args[] = '--include-path=' . implode(':', $paths);
        }

        $args[] = '{INPUT}';
        $args[] = '{OUTPUT}';

        // Run the filter
        $result = $this->runProcess($asset->getContent(), $args);
        $asset->setContent($result);
    }

    /**
     * @param AssetFactory $factory
     * @param $content
     * @param null $loadPath
     * @return array
     * @todo support for import-once
     * @todo support for import (less) "lib.css"
     */
    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        $loadPaths = $this->loadPaths;
        if (null !== $loadPath) {
            $loadPaths[] = $loadPath;
        }

        if (empty($loadPaths)) {
            return [];
        }

        $children = [];
        foreach (LessUtils::extractImports($content) as $reference) {
            if ('.css' === substr($reference, -4)) {
                // skip normal css imports
                // todo: skip imports with media queries
                continue;
            }

            if ('.less' !== substr($reference, -5)) {
                $reference .= '.less';
            }

            foreach ($loadPaths as $loadPath) {
                if (file_exists($file = $loadPath.'/'.$reference)) {
                    $coll = $factory->createAsset($file, [], array('root' => $loadPath));
                    foreach ($coll as $leaf) {
                        $leaf->ensureFilter($this);
                        $children[] = $leaf;
                        goto next_reference;
                    }
                }
            }

            next_reference:
        }

        return $children;
    }
}
