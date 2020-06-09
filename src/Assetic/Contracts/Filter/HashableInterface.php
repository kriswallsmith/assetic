<?php namespace Assetic\Contracts\Filter;

/**
 * A filter can implement a hash function
 *
 * @author Francisco Facioni <fran6co@gmail.com>
 */
interface HashableInterface
{
    /**
     * Generates a hash for the object
     *
     * @return string Object hash
     */
    public function hash();
}
