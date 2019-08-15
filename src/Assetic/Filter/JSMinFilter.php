<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;

/**
 * Filters assets through JsMin.
 *
 * All credit for the filter itself is mentioned in the file itself.
 *
 * @link https://raw.github.com/mrclay/minify/master/min/lib/JSMin.php
 * @author Brunoais <brunoaiss@gmail.com>
 */
class JSMinFilter extends BaseFilter
{
    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent(\JSMin::minify($asset->getContent()));
    }
}
