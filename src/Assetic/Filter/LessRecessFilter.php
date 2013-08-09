<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
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
 * Loads LESS files using the recess linter/compiler
 *
 * @link http://lesscss.org/
 * @link https://github.com/twitter/recess
 *
 * @author Botond Szasz <boteeka@gmail.com>
 */
class LessRecessFilter extends BaseNodeFilter implements DependencyExtractorInterface
{
    private $nodeBin;

    /**
     * @var array
     */
    private $parserOptions;

    /**
     * Constructor.
     *
     * @param string $nodeBin   The path to the node binary
     * @param array  $nodePaths An array of node paths
     */
    public function __construct($nodeBin = '/usr/bin/node', array $nodePaths = array())
    {
        $this->nodeBin = $nodeBin;
        $this->setNodePaths($nodePaths);
        $this->parserOptions = array('compile' => true);
    }

    /**
     * @param bool $compress
     */
    public function setCompress($compress)
    {
        $this->addParserOption('compress', $compress);
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
var recess = require('recess');
var sys  = require(process.binding('natives').util ? 'util' : 'sys');

recess(%s, %s, function(err, obj) {
    if (err) {
        throw err;
        process.exit(2);
    }
    try {
        sys.print(obj[0].output);
    } catch (e) {
        sys.print(obj[0].errors);
        process.exit(3);
    }
});

EOF;

        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();

        if ($root && $path) {
            $paths = array($root.'/'.$path);
        }

        foreach ($this->loadPaths as $loadPath) {
            $paths[] = $loadPath;
        }

        $pb = $this->createProcessBuilder();

        $pb->add($this->nodeBin)->add($input = tempnam(sys_get_temp_dir(), 'assetic_lessrecess'));
        file_put_contents($input, sprintf($format,
            json_encode($paths),
            json_encode($this->parserOptions)
        ));

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 !== $code) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent($proc->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }

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
