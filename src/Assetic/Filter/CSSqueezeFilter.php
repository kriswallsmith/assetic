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

/**
 * CSSqueeze filter.
 *
 * @link https://github.com/ianbogda/CSSqueeze
 * @author Yann Bogdanovic <ianbogda@gmail.com>
 */
class CSSqueezeFilter implements FilterInterface
{
    private $singleLine   = true;
    private $keepHack     = true;
    private $configuration= array();
    private $deflatIndent = '';

    public function setSingleLine($bool)
    {
        $this->singleLine = (bool) $bool;
    }

    public function setDeflatIndent($deflatIndent = null)
    {
        $this->deflatIndent = (string) $deflatIndent;
    }

    public function setConfiguration($configuration)
    {
        $this->configuration = (array) $configuration;
    }

    public function keepHack($bool)
    {
        $this->keepHack = (bool) $bool;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
		!isset($this->configuration['BasePath'])
			&& $this->setConfiguration(array('BasePath' => $asset->getSourceDirectory()));

        $parser = new \CSSqueeze(
            $this->deflatIndent,
            $this->configuration
        );
        $asset->setContent($parser->squeeze(
            $asset->getContent(),
            $this->singleLine,
            $this->keepHack
        ));
    }
}
