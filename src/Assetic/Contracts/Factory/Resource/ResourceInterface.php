<?php namespace Assetic\Contracts\Factory\Resource;

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
    public function isFresh($timestamp);

    /**
     * Returns the content of the resource.
     *
     * @return string The content
     */
    public function getContent();

    /**
     * Returns a unique string for the current resource.
     *
     * @return string A unique string to identity the current resource
     */
    public function __toString();
}
