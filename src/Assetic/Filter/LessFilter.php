<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Factory\AssetFactory;
use Assetic\Util\LessUtils;

/**
 * Loads LESS files.
 *
 * @link http://lesscss.org/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class LessFilter extends BaseProcessFilter implements DependencyExtractorInterface
{

    /**
     * Path to the Less binary, assumed in PATH
     *
     * @var string
     */
    private $lesscBin;

    /**
     * @var array
     */
    private $treeOptions;

    /**
     * @var array
     */
    private $parserOptions;

    /**
     * Load Paths
     *
     * A list of paths which less will search for includes.
     *
     * @var array
     */
    protected $loadPaths = array();

    /**
     * Constructor.
     *
     * @param string $nodeBin   The path to the node binary
     * @param array  $nodePaths An array of node paths
     */
    public function __construct()
    {
        $this->treeOptions = array(
            "compress" => false
        );
        $this->parserOptions = array();
    }

    /**
     * Sets the path to the Less binary
     *
     * @param string $path path to the Less binary
     */
    public function setLessBinary($lesscBin){
        $this->lesscBin = $lesscBin;
    }

    /**
     * @param bool $compress
     */
    public function setCompress($compress)
    {
        $this->treeOptions['compress'] = $compress;
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
     * @param string $code
     * @param string $value
     */
    public function addTreeOption($code, $value)
    {
        $this->treeOptions[$code] = $value;
    }

    /**
     * @param string $code
     * @param string $value
     */
    public function addParserOption($code, $value)
    {
        $this->parserOptions[$code] = $value;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $pb = $this->createProcessBuilder();
        $pb->inheritEnvironmentVariables();

        // the lessc binary
        $pb->add($this->lesscBin ?: 'lessc');

        // --compress, -x
        if($this->treeOptions['compress']){
            $pb->add("--compress");
        }

        // --include-path=PATHS
        // separated by : on unix, ; on Windows
        if($this->loadPaths){
            $loadPaths = join(PATH_SEPARATOR, $this->loadPaths);
            $pb->add("--include-path=" . $loadPaths);
        }

        $source_path = $asset->getSourcePath();
        if($source_path){
            // file asset, set as input
            $dir = $asset->getSourceDirectory();
            $pb->add($dir . DIRECTORY_SEPARATOR . $source_path);
        } else {
            // string asset, so use '-' to specify input from stdin
            $pb->add("-");
            $pb->setInput($asset->getContent());
        }

        $proc = $pb->getProcess();
        $code = $proc->run();

        if (0 !== $code) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent($proc->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    /**
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
            return array();
        }

        $children = array();
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
                    $coll = $factory->createAsset($file, array(), array('root' => $loadPath));
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
