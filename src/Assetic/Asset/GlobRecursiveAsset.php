<?php
/**
 * Author: Paul Vasiliev
 * Date: 8/8/13
 * Time: 4:26 PM
 */

namespace Assetic\Asset;


class GlobRecursiveAsset extends GlobAsset {

    protected function glob($pattern, $flags = 0)
    {
        $files = parent::glob($pattern, $flags);

        foreach (parent::glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, $this->glob($dir.'/'.basename($pattern), $flags));
        }

        return $files;
    }

}