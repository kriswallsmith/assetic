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
        $input = tempnam(sys_get_temp_dir(), 'assetic_coffeescript');
        file_put_contents($input, $asset->getContent());

        $pb = $this->createProcessBuilder($this->nodeBin
            ? array($this->nodeBin, $this->coffeeBin)
            : array($this->coffeeBin));

        $pb->add('-c');

        if ($this->bare) {
            $pb->add('--bare');
        }

        if ($this->noHeader) {
            $pb->add('--no-header');
        }

        $pb->add('--map');

        $pb->add($input);
        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 !== $code) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $content = file_get_contents($input.'.js');
        $contentMap = file_get_contents($input.'.js.map');
        unlink($input.'.js');
        unlink($input.'.js.map');

        $content = str_replace("\n".'//# sourceMappingURL='.basename($input).'.js.map'."\n", '', $content);


        $asset->setContent($content);

        $contentMap = json_decode($contentMap);
        unset($contentMap->file);
        unset($contentMap->sourceRoot);
        $contentMap->sources[0] = $asset->getSourceRoot().'/'.$asset->getSourcePath();
        $map = new \Kwf_SourceMaps_SourceMap($contentMap, $content);
        $asset->setContent($map->getFileContentsInlineMap());
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
