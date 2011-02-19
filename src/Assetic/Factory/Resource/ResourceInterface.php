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
 * Formulae can be loaded from a resource.
 *
 * A resource must be serializable.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface ResourceInterface
{
    /**
     * Returns the content of the resource.
     *
     * @return string The content
     */
    function getContent();

    /**
     * Checks if the supplied timestamp represents the freshest resource.
     *
     * @param integer $timestamp A UNIX timestamp
     *
     * @return Boolean True if the timestamp is fresh
     */
    function isFresh($timestamp);
}
