<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Resource;

/**
 * A resource is something formulae can be loaded from.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface ResourceInterface
{
    /**
     * Checks if a timestamp represents the latest resource.
     *
     * @param integer $timestamp A UNIX timestamp
     *
     * @return Boolean True if the timestamp is up to date
     */
    function isFresh($timestamp);

    /**
     * Returns the content of the resource.
     *
     * @return string The content
     */
    function getContent();
}
