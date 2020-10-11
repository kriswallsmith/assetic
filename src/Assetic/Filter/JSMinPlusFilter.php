<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;

/**
 * Filters assets through JSMinPlus.
 *
 * All credit for the filter itself is mentioned in the file itself.
 *
 * @link https://raw.github.com/mrclay/minify/master/min/lib/JSMinPlus.php
 * @author Brunoais <brunoaiss@gmail.com>
 */
class JSMinPlusFilter extends BaseFilter
{
    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent(\JSMinPlus::minify($asset->getContent()));
    }
}
