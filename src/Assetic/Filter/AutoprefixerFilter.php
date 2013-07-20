<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;

/**
 * Parses CSS and adds vendor prefixes to rules using values from the Can I Use website
 * 
 * @link https://github.com/ai/autoprefixer
 * @author Alex Vasilenko <aa.vasilenko@gmail.com>
 */
class AutoprefixerFilter extends BaseNodeFilter 
{
    /**
     * @var string
     */
    private $autoprefixerBin;

    /**
     * @var string
     */
    private $browsers;

    public function __construct($autoprefixerBin)
    {
        $this->autoprefixerBin = $autoprefixerBin;
    }

    /**
     * @param string $browser
     */
    public function setBrowsers($browser)
    {
        $this->browsers = $browser;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $input = $asset->getContent();
        $pb = $this->createProcessBuilder(array($this->autoprefixerBin, '-o', '-'));
        
        $pb->setInput($input);
        if ($this->browsers) {
            $pb->add('-b')->add($this->browsers);
        }
        $proc = $pb->getProcess();
        if (0 !== $proc->run()) {
            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }
        
        $asset->setContent($proc->getOutput());
    }

    /**
     * Filters an asset just before it's dumped.
     *
     * @param AssetInterface $asset An asset
     */
    public function filterDump(AssetInterface $asset)
    {
    }
}
