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
 * Iterates through assets and filters out duplicates.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AssetCollectionIterator extends \RecursiveFilterIterator
{
    private $urls = array();

    public function __construct(AssetCollection $coll)
    {
        parent::__construct($coll);
    }

    public function accept()
    {
        // no asset
        if (!$asset = $this->current()) {
            return false;
        }

        // no url == unique
        if (!$url = $asset->getUrl()) {
            return true;
        }

        // duplicate
        if (in_array($url, $this->urls)) {
            return false;
        }

        // remember we've been here
        $this->urls[] = $url;
        return true;
    }
}
