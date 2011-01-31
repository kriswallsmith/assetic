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

/**
 * Iterates through assets and filters out duplicates.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AssetCollectionIterator extends \RecursiveFilterIterator
{
    private $sourceUrls = array();

    public function __construct(AssetCollection $coll)
    {
        parent::__construct($coll);
    }

    public function accept()
    {
        $asset = $this->current();

        // no url == unique
        if (!$sourceUrl = $asset->getSourceUrl()) {
            return true;
        }

        // duplicate
        if (in_array($sourceUrl, $this->sourceUrls)) {
            return false;
        }

        // remember we've been here
        $this->sourceUrls[] = $sourceUrl;
        return true;
    }
}
