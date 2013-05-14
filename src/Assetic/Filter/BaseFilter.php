<?php

namespace Assetic\Filter;

use Assetic\Exception\FilterException;
use Symfony\Component\OptionsResolver;

abstract class BaseFilter implements FilterInterface
{
    /**
     * @see FilterInterface::setOptions()
     *
     * @throws \InvalidArgumentException If $options contains an option with non-string name
     * @throws FilterException If $options contains unsupported option
     * @param array $options
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            // require only string option name
            if (!is_string($option)) {
                throw new \InvalidArgumentException(get_class($this) . '::setOptions() expects option name to be string, "' . gettype($option) . '" is given');
            }

            $camelCaseOption = self::camelCase($option);
            $method = 'set' . $camelCaseOption;

            if (!method_exists($this, $method)) {
                throw new FilterException(get_class($this) . '::setOptions() unsupported option "' . $option . '", method "' . $method . '" was not found');
            }

            $this->$method($value);
        }
    }

    /**
     * @see FilterInterface::isOptionSupported()
     *
     * @param string $option
     * @return bool
     */
    public function isOptionSupported($option)
    {
        $camelCaseOption = self::camelCase($option);
        $method = 'set' . $camelCaseOption;

        return method_exists($this, $method);
    }

    /**
     * Makes provided string camelCased
     *
     * @param string $string
     * @return string
     */
    static private function camelCase($string)
    {
        $string = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        return $string;
    }
}
