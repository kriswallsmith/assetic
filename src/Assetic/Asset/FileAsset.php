<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset;

use Assetic\Filter\FilterInterface;

/**
 * Represents an asset loaded from a file.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FileAsset extends BaseAsset
{
    private $path;

    /**
     * Constructor.
     *
     * @param string $path      The absolute path to the asset
     * @param string $sourceUrl The source URL
     * @param array  $filters   Filters for the asset
     */
    public function __construct($path, $sourceUrl = null, $filters = array())
    {
        $this->path = $path;

        parent::__construct($sourceUrl, $filters);
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        $this->doLoad(file_get_contents($this->path), $additionalFilter);
    }

    public function getLastModified()
    {
        return filemtime($this->path);
    }
    
    public function getPath()
    {
        return $this->path;
    }
}
