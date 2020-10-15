<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Symfony\Component\Process\Process;

/**
 * An external process based filter which provides a way to set a timeout on the process.
 */
abstract class BaseProcessFilter implements FilterInterface
{
    private $timeout;

    /**
     * Set the process timeout.
     *
     * @param int $timeout The timeout for the process
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Creates a new process builder.
     *
     * @param array $arguments An optional array of arguments
     *
     * @return Process A new process builder
     */
    protected function createProcessBuilder(array $arguments = array())
    {
        $pb = new Process($arguments);

        if (null !== $this->timeout) {
            $pb->setTimeout($this->timeout);
        }

        return $pb;
    }

    protected function mergeEnv(Process $process)
    {
        $env = $process->getEnv();

        foreach (array_filter($_SERVER, 'is_scalar') as $key => $value) {
            $env[$key] = $value;
        }

        $process->setEnv($env);
    }
}
