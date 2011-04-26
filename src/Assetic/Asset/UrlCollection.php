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

/**
 * A collection of URLs or a single combined URL.
 *
 * This class allows the return value of the Assetic templating functions to
 * be used as either a single URL or looped over as many URLs.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class UrlCollection implements \IteratorAggregate, \Countable
{
    private $oneUrl;
    private $manyUrls;

    /**
     * Creates a new URL collection based on an asset collection.
     *
     * When debug mode is false there will only be one URL, regardless of
     * whether the collection is iterated over or echoed.
     *
     * @param AssetCollection $coll  An asset collection
     * @param Boolean         $debug The debug mode
     *
     * @return UrlCollection A new URL collection
     */
    static public function createFromAssetCollection(AssetCollection $coll, $debug = false)
    {
        if ($debug) {
            $manyUrls = array();
            foreach ($coll as $leaf) {
                $manyUrls[] = $leaf->getTargetUrl();
            }
        } else {
            $manyUrls = array($coll->getTargetUrl());
        }

        return new static($coll->getTargetUrl(), $manyUrls);
    }

    public function __construct($oneUrl, array $manyUrls)
    {
        $this->oneUrl = $oneUrl;
        $this->manyUrls = $manyUrls;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->manyUrls);
    }

    public function count()
    {
        return count($this->manyUrls);
    }

    public function __toString()
    {
        return (string) $this->oneUrl;
    }
}
