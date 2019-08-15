<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;

/**
 * Filters assets through Minify_CSS_Compressor.
 *
 * All credit for the filter itself is mentioned in the file itself.
 *
 * @link https://raw.githubusercontent.com/mrclay/minify/master/min/lib/Minify/CSS/Compressor.php
 * @author Stephen Clay <steve@mrclay.org>
 * @author http://code.google.com/u/1stvamp/ (Issue 64 patch)
 */
class MinifyCssCompressorFilter extends BaseFilter
{
    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent(\Minify_CSS_Compressor::process($asset->getContent()));
    }
}
