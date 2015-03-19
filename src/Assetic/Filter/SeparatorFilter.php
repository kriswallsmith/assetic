<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Inserts a separator between assets to prevent merge failures
 * e.g. missing semicolon at the end of a JS file
 *
 * @author Robin McCorkell <rmccorkell@karoshi.org.uk>
 */
class SeparatorFilter implements FilterInterface
{
    /**
     * @var string
     */
    private $separator;

    /**
     * Constructor.
     *
     * @param string $separator Separator to use between assets
     */
    public function __construct($separator = ';')
    {
        $this->separator = $separator;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent($asset->getContent() . $this->separator);
    }
}
