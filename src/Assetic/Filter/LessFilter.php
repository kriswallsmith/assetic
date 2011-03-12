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
class LessFilter implements FilterInterface
{
    private $baseDir;
    private $nodeBin;
    private $nodePaths;
    private $compress;

    /**
     * Constructor.
     *
     * @param string $baseDir   The base web directory
     * @param string $nodeBin   The path to the node binary
     * @param array  $nodePaths An array of node paths
     */
    public function __construct($baseDir, $nodeBin = '/usr/bin/node', array $nodePaths = array())
    {
        $this->baseDir = $baseDir;
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

        $sourceUrl = $asset->getSourceUrl();

        // parser options
        $parserOptions = array();
        if ($sourceUrl && false === strpos($sourceUrl, '://')) {
            $baseDir = self::isAbsolutePath($sourceUrl) ? '' : $this->baseDir.'/';

            $parserOptions['paths'] = array($baseDir.dirname($sourceUrl));
            $parserOptions['filename'] = basename($sourceUrl);
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

        $options[] = $input = tempnam(sys_get_temp_dir(), 'assetic_less');
        file_put_contents($input, sprintf($format,
            json_encode($parserOptions),
            json_encode($asset->getContent()),
            json_encode($treeOptions)
        ));

        $proc = new Process(implode(' ', array_map('escapeshellarg', $options)), null, $env);
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            throw new \RuntimeException($proc->getErrorOutput());
        }

        $asset->setContent($proc->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    static private function isAbsolutePath($path)
    {
        return '/' == $path[0] || '\\' == $path[0] || (3 < strlen($path) && ctype_alpha($path[0]) && $path[1] == ':' && ('\\' == $path[2] || '/' == $path[2]));
    }
}
