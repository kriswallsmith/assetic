<?php namespace Assetic\Filter;

use Symfony\Component\Process\Process;
use Assetic\Contracts\Filter\FilterInterface;

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
    protected function createProcess(array $arguments = array())
    {
        $process = new Process($arguments);

        if (null !== $this->timeout) {
            $process->setTimeout($this->timeout);
        }

        return $process;
    }

    protected function mergeEnv(Process $process)
    {
        foreach (array_filter($_SERVER, 'is_scalar') as $key => $value) {
            $process->setEnv([$key => $value]);
        }
    }
}
