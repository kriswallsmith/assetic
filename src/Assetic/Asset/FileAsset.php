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
    public function load(FilterInterface $additionalFilter = null)
    {
        $this->doLoad(file_get_contents($this->getSourceUrl()), $additionalFilter);
    }

    public function getLastModified()
    {
        return filemtime($this->getSourceUrl());
    }
}
