<?php

namespace Assetic\Asset;

/**
 * A collection of assets loaded by glob.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class GlobAsset extends AssetCollection
{
    /**
     * Constructor.
     *
     * @param string $glob A glob path
     */
    public function __construct($glob, $filters = array())
    {
        $assets = array();
        if (false !== $paths = glob($glob)) {
            foreach ($paths as $path) {
                $assets[] = new FileAsset($path);
            }
        }

        parent::__construct($assets, $filters);
    }
}
