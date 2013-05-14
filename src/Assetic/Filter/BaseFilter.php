<?php

namespace Assetic\Filter;

use Assetic\Exception\FilterException;

abstract class BaseFilter implements FilterInterface
{
    /**
     * @see FilterInterface::setOptions()
     *
     * @throws FilterException If $options contains invalid/unsupported option name
     * @param array $options
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            // require only string option name
            if (!is_string($option)) {
                throw new FilterException(get_class($this) . '::setOptions() expects option name to be string, "' . gettype($option) . '" is given');
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
     * @param $option
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
     * @param $string
     * @param bool $capitalizeFirstCharacter
     * @param string $separator
     * @return string
     */
    static private function camelCase($string, $capitalizeFirstCharacter = true, $separator = '_')
    {
        $string = str_replace(' ', '', ucwords(str_replace($separator, ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $string[0] = strtolower($string[0]);
        }

        return $string;
    }
}
