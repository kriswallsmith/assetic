<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Precompiles Twig templates into Javascript for Twig.js
 *
 * @link https://github.com/justjohn/twig.js
 * @author Ketil Albertsen <ketil@syscom.as>
 */
class TwigFilter implements FilterInterface
{
    protected $nodeBin;
    protected $nodePaths;

    /**
     * Constructs filter.
     *
     * @param string $nodeBin   The path to the node binary
     * @param array  $nodePaths An array of node paths
     */
    public function __construct($nodeBin = '/usr/bin/node', array $nodePaths = null)
    {
        $this->nodeBin = $nodeBin;
        $this->nodePaths = $nodePaths;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        // generate js compiler
        $path = $asset->getSourcePath();
        $js   = $this->getCompilerCode($path, $asset->getContent());

        $input = tempnam(sys_get_temp_dir(), 'twigjs');
        file_put_contents($input, $js);

        // create process
        $pb = new ProcessBuilder(array($this->nodeBin, $input));

        if (!empty($this->nodePaths)) {
            $pb->setEnv('NODE_PATH', implode(PATH_SEPARATOR, $this->nodePaths));
        }

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $compiled = trim($proc->getOutput());
        $asset->setContent($compiled);
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
    }

    /**
     * Get Javascript code for template compiler
     *
     * @param string $name
     * @param string $data
     * @return string
     */
    protected function getCompilerCode($name, $data)
    {
        $options = json_encode(array('id' => $name, 'data' => $data));

        return <<<"EOL"
var twig = require('twig').twig;
var js = twig($options).compile({});
console.log(js);

EOL;
    }
}
