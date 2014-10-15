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

    public function setDeflatIndent(string $deflatIndent)
    {
        $this->deflatIndent = $deflatIndent;
    }

    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function keepHack(bool $bool)
    {
        $this->keepHack = $bool;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        if (!isset($this->configuration['BasePath']))
        {
            $this->configuration['BasePath'] = $asset->getSourceDirectory();
        }

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
