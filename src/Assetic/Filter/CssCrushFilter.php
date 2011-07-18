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
use Assetic\Filter\FilterInterface;

/**
 * Loads CssCrush files.
 *
 * @link http://the-echoplex.net/csscrush/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * @todo import directives do not work
 */
class CssCrushFilter implements FilterInterface
{
    private $debug = false;
    private $boilerplate;
    private $versioning = false;

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    public function setBoilerplate($boilerplate)
    {
        $this->boilerplate = $boilerplate;
    }

    public function setVersioning($versioning)
    {
        $this->versioning = $versioning;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $options = array();

        if (null !== $this->debug) {
            $options['debug'] = (Boolean) $this->debug;
        }

        if (null !== $this->boilerplate) {
            $options['boilerplate'] = (Boolean) $this->boilerplate;
        }

        if (null !== $this->versioning) {
            $options['versioning'] = (Boolean) $this->versioning;
        }

        // remember the previous document root
        $snapshot = \CssCrush::$config->docRoot;

        // setup the input
        $input = tempnam(sys_get_temp_dir(), 'assetic_csscrush');
        file_put_contents($input, $asset->getContent());

        // process the asset
        \CssCrush::$config->docRoot = dirname($input);
        $output = \CssCrush::file('/'.basename($input), $options);
        $asset->setContent(file_get_contents(\CssCrush::$config->docRoot.$output));

        // cleanup
        unlink($input);
        unlink(\CssCrush::$config->docRoot.$output);
        \CssCrush::$config->docRoot = $snapshot;
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
