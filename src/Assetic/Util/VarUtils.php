<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Util;

/**
 * Variable utilities.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class VarUtils
{
    /**
     * Resolves variable placeholders.
     *
     * @param string $template A template string
     * @param array  $vars     Variable names
     * @param array  $values   Variable values
     *
     * @return string The resolved string
     *
     * @throws \InvalidArgumentException If there is a variable with no value
     */
    public static function resolve($template, array $vars, array $values)
    {
        $map = array();
        foreach ($vars as $var) {
            if (false === strpos($template, '{'.$var.'}')) {
                continue;
            }

            if (!isset($values[$var])) {
                throw new \InvalidArgumentException(sprintf('The path "%s" contains the variable "%s", but was not given any value for it.', $template, $var));
            }

            $map['{'.$var.'}'] = $values[$var];
        }

        return strtr($template, $map);
    }

    final private function __construct() { }
}
