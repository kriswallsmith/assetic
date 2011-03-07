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
 * Represents a string asset.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class StringAsset extends BaseAsset
{
    private $originalContent;
    private $lastModified;

    /**
     * Constructor.
     *
     * @param string $content   The content of the asset
     * @param array  $filters   Filters for the asset
     * @param string $sourceUrl The source URL
     */
    public function __construct($content, $filters = array(), $sourceUrl = null)
    {
        $this->originalContent = $content;

        parent::__construct($filters, $sourceUrl);
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        $this->doLoad($this->originalContent, $additionalFilter);
    }

    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }

    public function getLastModified()
    {
        return $this->lastModified;
    }
}
