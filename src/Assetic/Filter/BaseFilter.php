<?php

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

abstract class BaseFilter implements FilterInterface
{
    /**
     * @see FitlerInterface::__construct()
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Automatically calls setter methods based on key of option
     *
     * @param array $options
     */
    public function setOptions(array $options = array())
    {
        foreach ($options as $option => $value) {
            // test for string option name
            if (!is_string($option)) {
                throw new \Exception(get_class($this) . '::setOptions() expects option name to be string, "' . gettype($option) . '" is given');
            }

            // TODO: do some optimization, regex is too slow
            $_option = preg_replace('/(?:^|_)(.?)/e', "strtoupper('$1')", $option);
            $method = 'set' . $_option;

            if (!method_exists($this, $method)) {
                throw new \Exception(get_class($this) . '::setOptions() unsupported option "' . $option . '", method "' . $method . '" was not found');
            }

            $this->$method($value);
        }
    }

    /**
     * Filters an asset after it has been loaded.
     *
     * @param AssetInterface $asset An asset
     */
    abstract public function filterLoad(AssetInterface $asset);

    /**
     * Filters an asset just before it's dumped.
     *
     * @param AssetInterface $asset An asset
     */
    abstract public function filterDump(AssetInterface $asset);
}
