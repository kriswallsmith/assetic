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
 * Loads STYL files.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class StylusFilter implements FilterInterface
{
    private $baseDir;
    private $nodeBin;
    private $nodePaths;
    private $compress;

    /**
     * Constructs filter.
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

    /**
     * Enable output compression.
     *
     * @param   boolean     $compress
     */
    public function setCompress($compress)
    {
        $this->compress = $compress;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        static $format = <<<'EOF'
var stylus = require('stylus');
var sys    = require('sys');

stylus(%s, %s).render(function(e, css){
    if (e) {
        throw e;
    }

    sys.print(css);
    process.exit(0);
});

EOF;

        // parser options
        $parserOptions = array();
        if ($sourceUrl = $asset->getSourceUrl()) {
            $parserOptions['filename']  = basename($sourceUrl);
            $parserOptions['paths']     = array($this->baseDir . DIRECTORY_SEPARATOR . dirname($sourceUrl));
        }

        if (null !== $this->compress) {
            $parserOptions['compress'] = $this->compress;
        }

        // node.js configuration
        $env = array();
        if (0 < count($this->nodePaths)) {
            $env['NODE_PATH'] = implode(':', $this->nodePaths);
        }

        $options = array($this->nodeBin);
        $options[] = $input = tempnam(sys_get_temp_dir(), 'assetic_stylus');
        file_put_contents($input, sprintf($format,
            json_encode($asset->getContent()),
            json_encode($parserOptions)
        ));

        $proc = new Process(implode(' ', array_map('escapeshellarg', $options)), null, $env);
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            throw new \RuntimeException($proc->getErrorOutput());
        }

        $asset->setContent($proc->getOutput());
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
    }
}
