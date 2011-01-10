<?php

namespace Assetic\Filter\Yui;

use Assetic\Asset\AssetInterface;

/**
 * CSS YUI compressor filter.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class YuiCompressorCssFilter extends BaseYuiCompressorFilter
{
    public function filterDump(AssetInterface $asset)
    {
        $asset->setBody($this->compress($asset->getBody(), 'css'));
    }
}
