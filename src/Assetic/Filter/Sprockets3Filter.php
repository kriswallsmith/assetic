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
use Assetic\Util\FilesystemUtils;

/**
 * Runs assets through Sprockets3.
 * Requires Sprockets 3.x.
 *
 * @link https://github.com/rails/sprockets/tree/3.x
 * @link https://rubygems.org/gems/sprockets
 *
 * @author Igor Hlina <srigi@srigi.sk>
 */
class Sprockets3Filter extends BaseProcessFilter
{
    /** @var array  */
    private $includeDirs = array();

    /** @var string  */
    private $rubyBin;

    /** @var NULL|string  */
    private $sprockets3Lib;


    /**
     * @param string $sprocketsLib Path to the Sprockets lib/ directory
     * @param string $rubyBin      Path to the ruby binary
     */
    public function __construct($sprocketsLib = null, $rubyBin = '/usr/bin/ruby')
    {
        $this->sprockets3Lib = $sprocketsLib;
        $this->rubyBin = $rubyBin;
    }

    /**
     * @param string $directory
     */
    public function addIncludeDir($directory)
    {
        $this->includeDirs[] = $directory;
    }

    /**
     * @param AssetInterface $asset
     * @return string
     */
    public function filterLoad(AssetInterface $asset)
    {
        $scriptSrc = <<<EOF
#!/usr/bin/env ruby

require %s

environment = Sprockets::Environment.new
%s

print environment.find_asset(%s)

EOF;
        $sprocket3LibImport = ($this->sprockets3Lib)
            ? sprintf("File.join(%s, 'sprockets')", var_export($this->sprockets3Lib, true))
            : "sprockets";
        $appends = array_reduce($this->includeDirs, function($memo, $dir) {
            $memo .= 'environment.append_path('. var_export($dir, true) .")\n";
            return $memo;
        });
        $script = FilesystemUtils::createTemporaryFile('sprockets3_script_');
        $scriptSrc = sprintf($scriptSrc,
            $sprocket3LibImport,
            $appends,
            var_export(basename($asset->getSourcePath()), true)
        );
        file_put_contents($script, $scriptSrc);

        $processBuilder = $this->createProcessBuilder(array($this->rubyBin, $script));
        $process = $processBuilder->getProcess();
        $exitCode = $process->run();
        unlink($script);

        if ($exitCode !== 0) {
            throw FilterException::fromProcess($process)->setInput($asset->getContent());
        }

        $result = $process->getOutput();
        $asset->setContent($result);

        return $result;
    }

    /**
     * @param AssetInterface $asset
     */
    public function filterDump(AssetInterface $asset)
    {
    }
}
