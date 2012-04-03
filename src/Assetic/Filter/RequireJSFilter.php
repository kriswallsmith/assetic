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
 * Filter for RequireJS
 *
 * @author Daniel Cannon <daniel@danielcannon.co.uk>
 */
class RequireJsFilter implements FilterInterface
{
    private $nodeBin;
    private $nodePaths;

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

    public function filterLoad(AssetInterface $asset)
    {
        static $format = "
var require = require('requirejs');
var fs = require('fs');
var sys  = require(process.binding('natives').util ? 'util' : 'sys');
var config = %s;

try {
    require.optimize(config, function (buildResponse) {
        var contents = fs.readFileSync(config.out, 'utf8');
        sys.print(contents);
    });
} catch (e) {
    sys.print(e.toString());
}
";

        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();
        $input = tempnam(sys_get_temp_dir(), 'assetic_requirejs_input');
        $output = tempnam(sys_get_temp_dir(), 'assetic_requirejs_output');

        // requirejs config
        $config = array();
        if ($root && $path) {
            $info = pathinfo($root.'/'.$path);
            $config['name'] = basename($path,'.'.$info['extension']);
            $config['mainConfigFile'] = $root.'/'.$path;
            $config['out'] = $output;
        }

        file_put_contents($input, sprintf($format,
            json_encode($config)
        ));

        $pb = new ProcessBuilder();
        $pb->inheritEnvironmentVariables();
        $pb->add($this->nodeBin)->add($input);

        $proc = $pb->getProcess();
        $proc->run();

        unlink($input);
        unlink($output);

        if (0 < $proc->getExitCode()) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent($proc->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
