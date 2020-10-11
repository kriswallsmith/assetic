<?php namespace Assetic\Contracts;

/**
 * Value Supplier Interface.
 *
 * Implementations determine runtime values for compile-time variables.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface ValueSupplierInterface
{
    /**
     * Returns a map of values.
     *
     * @return array
     */
    public function getValues();
}
