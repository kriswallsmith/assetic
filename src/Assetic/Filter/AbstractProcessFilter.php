<?php
/**
 * Author: Costin Bereveanu <cbereveanu@gmail.com>
 * Date: 25.05.2011 10:42
 */

namespace Assetic\Filter;

use Assetic\Util\Process;

/**
 * Abstract class to be extended by filters making use of processes.
 *
 * @author Costin Bereveanu <cbereveanu@gmail.com>
 */
abstract class AbstractProcessFilter implements FilterInterface
{
    private $processReturnCode = null;
    private $processErrorOutput = '';
    private $processOutput = '';
    private $escapingAlgorithm = null;

    /**
     * @param array $options
     * @return int
     * @throw \RuntimeException
     */
    protected function runProcess($options)
    {
        if (null == $this->escapingAlgorithm) {
            $this->escapingAlgorithm = function($option) {
                return false !== strpos($option, ' ') ? escapeshellarg($option) : $option;
            };
        }

        $process = new Process(implode(' ', array_map($this->escapingAlgorithm, $options)));
        $this->processReturnCode = $process->run();
        $this->processErrorOutput = $process->getErrorOutput();
        $this->processOutput = $process->getOutput();

        if (0 < $this->processReturnCode) {
            throw new \RuntimeException($this->processErrorOutput);
        }

        return $this->getProcessReturnCode();
    }

    /**
     * @return int
     */
    public function getProcessReturnCode()
    {
        return $this->processReturnCode;
    }

    /**
     * @return string
     */
    public function getProcessErrorOutput()
    {
        return $this->getProcessErrorOutput();
    }

    /**
     * @return string
     */
    public function getProcessOutput()
    {
        return $this->getProcessOutput();
    }

    /**
     * @param Closure $method
     * @return void
     */
    public function setEscapingAlgorithm($method)
    {
        $this->escapingAlgorithm = $method;
    }

    /**
     * @return string
     */
    protected function getTempDir()
    {
        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
    }
}
