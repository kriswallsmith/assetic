<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Loads LESS files.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class LessFilter extends AbstractProcessFilter
{
    private $nodeBin;
    private $nodePaths;
    private $compress;

    /**
     * Constructor.
     *
     * @param string $nodeBin   The path to the node binary
     * @param array  $nodePaths An array of node paths
     */
    public function __construct($nodeBin = '/usr/bin/node', array $nodePaths = array())
    {
        $this->nodeBin = $nodeBin;
        $this->nodePaths = $nodePaths;
    }

    public function setCompress($compress)
    {
        $this->compress = $compress;
    }

    public function filterLoad(AssetInterface $asset)
    {
        static $format = <<<'EOF'
var less = require('less');
var sys  = require('sys');

new(less.Parser)(%s).parse(%s, function(e, tree) {
    if (e) {
        less.writeError(e);
        process.exit(2);
    }

    try {
        sys.print(tree.toCSS(%s));
        process.exit(0);
    } catch (e) {
        less.writeError(e);
        process.exit(3);
    }
});

EOF;

        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();

        // parser options
        $parserOptions = array();
        if ($root && $path) {
            $parserOptions['paths'] = array(dirname($root.'/'.$path));
            $parserOptions['filename'] = basename($path);
        }

        // tree options
        $treeOptions = array();
        if (null !== $this->compress) {
            $treeOptions['compress'] = $this->compress;
        }

        // node.js configuration
        $env = array();
        if (0 < count($this->nodePaths)) {
            $env['NODE_PATH'] = implode(':', $this->nodePaths);
        }

        $options = array($this->nodeBin);

        $options[] = $input = tempnam(self::getTempDir(), 'assetic_less');
        file_put_contents($input, sprintf($format,
            json_encode($parserOptions),
            json_encode($asset->getContent()),
            json_encode($treeOptions)
        ));

        $process = $this->createProcess($options);
        $code = $process->run();
        unlink($input);
        if (0 < $code) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        $asset->setContent($process->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
