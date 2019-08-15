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
    private $nodeBin;

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
    protected $loadPaths = [];

    /**
     * Constructor.
     *
     * @param string $nodeBin   The path to the node binary
     * @param array  $nodePaths An array of node paths
     */
    public function __construct($nodeBin = '/usr/bin/node', array $nodePaths = [])
    {
        $this->nodeBin = $nodeBin;
        $this->setNodePaths($nodePaths);
        $this->treeOptions = [];
        $this->parserOptions = [];
    }

    /**
     * @param bool $compress
     */
    public function setCompress($compress)
    {
        $this->addTreeOption('compress', $compress);
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
        static $format = <<<'EOF'
var less = require('less');
var sys  = require(process.binding('natives').util ? 'util' : 'sys');

less.render(%s, %s, function(error, css) {
    if (error) {
        less.writeError(error);
        process.exit(2);
    }
    try {
        if (typeof css == 'string') {
            sys.print(css);
        } else {
            sys.print(css.css);
        }
    } catch (e) {
        less.writeError(error);
        process.exit(3);
    }
});

EOF;

        // parser options
        $parserOptions = $this->parserOptions;
        if ($dir = $asset->getSourceDirectory()) {
            $parserOptions['paths'] = array($dir);
            $parserOptions['filename'] = basename($asset->getSourcePath());
        }

        foreach ($this->loadPaths as $loadPath) {
            $parserOptions['paths'][] = $loadPath;
        }

        $input = FilesystemUtils::createTemporaryFile('less', sprintf($format,
            json_encode($asset->getContent()),
            json_encode(array_merge($parserOptions, $this->treeOptions))
        ));

        $process = $this->createProcess([$this->nodeBin, $input]);

        $code = $process->run();
        unlink($input);

        if (0 !== $code) {
            throw FilterException::fromProcess($process)->setInput($asset->getContent());
        }

        $asset->setContent($process->getOutput());
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
