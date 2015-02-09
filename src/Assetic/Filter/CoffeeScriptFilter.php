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

/**
 * Compiles CoffeeScript into Javascript.
 *
 * @link http://coffeescript.org/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CoffeeScriptFilter extends BaseProcessFilter
{
    private $coffeeBin;

    // coffee options
    private $bare;
    private $noHeader;

    public function __construct($coffeeBin = 'coffee')
    {
        $this->coffeeBin = $coffeeBin;
    }

    public function setBare($bare)
    {
        $this->bare = $bare;
    }

    public function setNoHeader($noHeader)
    {
        $this->noHeader = $noHeader;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $pb = $this->createProcessBuilder();
        $pb->inheritEnvironmentVariables();

        // the coffee binary
        $pb->add($this->coffeeBin);

        if ($this->bare) {
            $pb->add('--bare');
        }

        if ($this->noHeader) {
            $pb->add('--no-header');
        }

        // compile to JavaScript, otherwise coffee will run the script
        $pb->add('--compile');

        // we want it to print the output rather than run it
        $pb->add('--print');

        // read content from standard input
        $pb->add("--stdio");

        // set the input to be sent to stdin as the current asset content,
        // to maintain the ability to chain filters
        $pb->setInput($asset->getContent());

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
}
