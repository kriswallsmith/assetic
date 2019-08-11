<?php namespace Assetic\Contracts\Factory\Loader;

use Assetic\Contracts\Factory\Resource\ResourceInterface;

/**
 * Loads formulae.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface FormulaLoaderInterface
{
    /**
     * Loads formulae from a resource.
     *
     * Formulae should be loaded the same regardless of the current debug
     * mode. Debug considerations should happen downstream.
     *
     * @param ResourceInterface $resource A resource
     *
     * @return array An array of formulae
     */
    public function load(ResourceInterface $resource);
}
