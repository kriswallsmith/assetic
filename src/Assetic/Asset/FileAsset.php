<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
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
     * @param array  $filters   Filters for the asset
     * @param string $sourceUrl The source URL
     */
    public function __construct($path, $filters = array(), $sourceUrl = null)
    {
        $this->path = $path;

        parent::__construct($filters, $sourceUrl);
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        $this->doLoad(file_get_contents($this->path), $additionalFilter);
    }

    public function getLastModified()
    {
        return filemtime($this->path);
    }
}
