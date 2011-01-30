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
    private $originalBody;
    private $lastModified;

    /**
     * Constructor.
     *
     * @param string $body    The body of the asset
     * @param string $url     The asset URL
     * @param array  $filters Filters for the asset
     */
    public function __construct($body, $url = null, $filters = array())
    {
        parent::__construct($filters);

        $this->originalBody = $body;
        $this->setUrl($url);
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        $this->doLoad($this->originalBody, $additionalFilter);
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
