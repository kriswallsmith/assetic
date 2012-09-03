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

/**
 * Loads LESS files using the PHP implementation of less, lessphp.
 *
 * Less files are mostly compatible, but there are slight differences.
 *
 * @link http://leafo.net/lessphp/
 *
 * @author David Buchmann <david@liip.ch>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class LessphpFilter implements FilterInterface
{
    private $presets = array();

    /**
     * Lessphp Load Paths
     * 
     * @var array
     */
    protected $loadPaths = array();

    /**
     * Adds a load path to the paths used by lessphp
     * 
     * @param string $path Load Path
     */
    public function addLoadPath($path)
    {
        $this->loadPaths[] = $path;
    }

    public function setPresets(array $presets)
    {
        $this->presets = $presets;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();

        $lc = new \lessc();
        if ($root && $path) {
            $lc->importDir = dirname($root.'/'.$path);
        }
        foreach ($this->loadPaths as $loadPath) {
            $lc->addImportDir($loadPath);
        }

        $asset->setContent($lc->parse($asset->getContent(), $this->presets));
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
