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

/**
 * Runs assets through pngout.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class PngoutFilter extends AbstractProcessFilter
{
    // -c#
    const COLOR_GREY       = '0';
    const COLOR_RGB        = '2';
    const COLOR_PAL        = '3';
    const COLOR_GRAY_ALPHA = '4';
    const COLOR_RGB_ALPHA  = '6';

    // -f#
    const FILTER_NONE  = '0';
    const FILTER_X     = '1';
    const FILTER_Y     = '2';
    const FILTER_X_Y   = '3';
    const FILTER_PAETH = '4';
    const FILTER_MIXED = '5';

    // -s#
    const STRATEGY_XTREME        = '0';
    const STRATEGY_INTENSE       = '1';
    const STRATEGY_LONGEST_MATCH = '2';
    const STRATEGY_HUFFMAN_ONLY  = '3';
    const STRATEGY_UNCOMPRESSED  = '4';

    private $pngoutBin;
    private $color;
    private $filter;
    private $strategy;
    private $blockSplitThreshold;

    /**
     * Constructor.
     *
     * @param string $pngoutBin Path to the pngout binary
     */
    public function __construct($pngoutBin = '/usr/bin/pngout')
    {
        $this->pngoutBin = $pngoutBin;
    }

    public function setColor($color)
    {
        $this->color = $color;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;
    }

    public function setBlockSplitThreshold($blockSplitThreshold)
    {
        $this->blockSplitThreshold = $blockSplitThreshold;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $options = array($this->pngoutBin);

        if (null !== $this->color) {
            $options[] = '-c'.$this->color;
        }

        if (null !== $this->filter) {
            $options[] = '-f'.$this->filter;
        }

        if (null !== $this->strategy) {
            $options[] = '-s'.$this->strategy;
        }

        if (null !== $this->blockSplitThreshold) {
            $options[] = '-b'.$this->blockSplitThreshold;
        }

        $options[] = $input = tempnam(sys_get_temp_dir(), 'assetic_pngout');
        file_put_contents($input, $asset->getContent());

        $output = tempnam(sys_get_temp_dir(), 'assetic_pngout');
        unlink($output);
        $options[] = $output .= '.png';

        try {
            $this->runProcess($options);
        } catch (\RuntimeException $ex) {
            unlink($input);
            throw $ex;
        }

        $asset->setContent(file_get_contents($output));

        unlink($input);
        unlink($output);
    }
}
