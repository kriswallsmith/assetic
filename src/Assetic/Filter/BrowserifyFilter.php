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
use Assetic\Factory\AssetFactory;
use Assetic\Filter\BaseNodeFilter;
use Assetic\Exception\FilterException;
use Assetic\Filter\DependencyExtractorInterface;

/**
 * Browserify filter.
 *
 * @link http://lisperator.net/uglifyjs
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class BrowserifyFilter extends BaseNodeFilter implements DependencyExtractorInterface
{
    private $browserifyBin;
    private $nodeBin;

    protected $loadPaths = array();

    public function __construct($browserifyBin = '/usr/bin/browserify', $nodeBin = '/usr/bin/node')
    {
        $this->browserifyBin = $browserifyBin;
        $this->nodeBin = $nodeBin;
        $this->addNodePath(dirname(dirname(realpath($browserifyBin))));
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $pb = $this->createProcessBuilder(
            $this->nodeBin
            ? array($this->nodeBin, $this->browserifyBin)
            : array($this->browserifyBin)
        );

        $input = realpath($asset->getSourceRoot() . DIRECTORY_SEPARATOR . $asset->getSourcePath());
        $output = tempnam(sys_get_temp_dir(), 'output');

        $pb->add($input)->add('-o')->add($output);

        $proc = $pb->getProcess();
        $code = $proc->run();

        if (0 !== $code) {
            if (file_exists($output)) {
                unlink($output);
            }

            if (127 === $code) {
                throw new \RuntimeException('Path to node executable could not be resolved.');
            }

            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        if (!file_exists($output)) {
            throw new \RuntimeException('Error creating output file.');
        }

        $asset->setContent(file_get_contents($output));

        unlink($output);
    }

    /**
     * Gets Browserify module dependencies using `list` argument from cmd.js
     *
     * Ref. https://github.com/substack/node-browserify/blob/master/bin/cmd.js#L45
     *
     * @param  AssetFactory $factory
     * @param  string       $content
     * @param  string       $loadPath
     *
     * @return array
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

        $format = <<<'JS'
var Readable = require('stream').Readable;
var fs = require('fs');
var browserify = require('index');
var through = require('node_modules/through2');

var rs = new Readable();
rs.push(%s);
rs.push(null);

var b = browserify({
    entries: rs,
    basedir: %s
});
b.pipeline.get('deps').push(through.obj(
    function (row, enc, next) {
        console.log(row.file || row.id);
        next();
    }
));
return b.bundle();
JS;

        $pb = $this->createProcessBuilder(array($this->nodeBin));

        $input = tempnam(sys_get_temp_dir(), 'browserify_deps_list');
        file_put_contents($input, sprintf($format,
            json_encode($content),
            json_encode($loadPaths[0])
        ));

        $pb->add($input);

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 !== $code) {
            if (127 === $code) {
                throw new \RuntimeException('Path to node executable could not be resolved.');
            }
        }

        $output = $proc->getOutput();
        $output = explode(PHP_EOL, $output);

        foreach ($output as $file) {
            if ($file === '' || preg_match('/_stream_1.js$/', $file) || preg_match('/\/node_modules\/browserify\/node_modules\//', $file)) {
                continue;
            }
            $coll = $factory->createAsset($file, array(), array('root' => $loadPaths[0]));
            foreach ($coll as $leaf) {
                $children[] = $leaf;
            }
        }

        return $children;
    }
}
