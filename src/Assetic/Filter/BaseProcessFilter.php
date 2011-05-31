<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Util\Process;

/**
 * An abstract filter for filters that use another process.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
abstract class BaseProcessFilter implements FilterInterface
{
    /**
     * Creates a new process.
     *
     * @param array  $parts The parts of the command
     * @param array  $env   Environment variables
     * @param string $stdin Standard input
     *
     * @return Process A new process
     */
    protected function createProcess(array $parts, array $env = null, $stdin = null)
    {
        return new Process(implode(' ', array_map(array($this, 'escape'), $parts)), null, $env, $stdin);
    }

    /**
     * Escapes a shell argument.
     *
     * @param string $value The value to escape
     *
     * @return The escaped value
     */
    protected function escape($value)
    {
        return false === strpos($value, ' ') ? $value : escapeshellarg($value);
    }
}
