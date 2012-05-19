<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Core\Source;

/**
 * The source represents the asset source file.
 */
interface SourceInterface
{
    /**
     * Returns the path to the source.
     *
     * @return string The path
     */
    function getPath();

    /**
     * Returns the content.
     *
     * @return string The content
     */
    function getContent();

    /**
     * Returns the timestamp when this source was last modified.
     *
     * @return integer A UNIX timestamp
     */
    function getLastModified();

    /**
     * Returns an array of extensions for the current asset.
     *
     * @return array An array of extensions
     */
    function getExtensions();
}
