<?php

namespace Assetic\Asset;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
