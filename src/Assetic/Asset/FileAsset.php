<?php

namespace Assetic\Asset;

/**
 * Represents an asset loaded from a file.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FileAsset extends Asset
{
    /**
     * Constructor.
     *
     * @param string $path    The absolute file system path
     * @param array  $filters Filters for the asset
     */
    public function __construct($path, $filters = array())
    {
        parent::__construct(file_get_contents($path), $filters);
    }
}
