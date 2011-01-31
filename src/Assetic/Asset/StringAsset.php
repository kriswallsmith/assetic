<?php

namespace Assetic\Asset;

use Assetic\Filter\FilterInterface;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @param string $sourceUrl The source URL
     * @param array  $filters   Filters for the asset
     */
    public function __construct($content, $sourceUrl = null, $filters = array())
    {
        $this->originalContent = $content;

        parent::__construct($sourceUrl, $filters);
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
