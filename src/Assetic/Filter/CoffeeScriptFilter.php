<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;

/**
 * Compiles CoffeeScript into Javascript.
 *
 * @link http://coffeescript.org/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CoffeeScriptFilter extends BaseNodeFilter
{
    private $coffeeBin;
    private $nodeBin;

    // coffee options
    private $bare;
    private $noHeader;

    public function __construct($coffeeBin = '/usr/bin/coffee', $nodeBin = null)
    {
        $this->coffeeBin = $coffeeBin;
        $this->nodeBin = $nodeBin;
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
        $input = FilesystemUtils::createTemporaryFileAndWrite('coffee', $asset->getContent());

        $args = $this->nodeBin
            ? array($this->nodeBin, $this->coffeeBin)
            : array($this->coffeeBin);

        $args[] = '-cp';

        if ($this->bare) {
            $args[] = '--bare';
        }

        if ($this->noHeader) {
            $args[] = '--no-header';
        }

        $args[] = $input;

        $process = $this->createProcess($args);

        $code = $process->run();
        unlink($input);

        if (0 !== $code) {
            throw FilterException::fromProcess($process)->setInput($asset->getContent());
        }

        $asset->setContent($process->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
