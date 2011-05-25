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
    private $escapingAlgorithm = null;

    /**
     * @param array $options
     * @return \Assetic\Util\Process
     */
    protected function createProcess($options)
    {
        if (null == $this->escapingAlgorithm) {
            $this->escapingAlgorithm = function($option) {
                return false !== strpos($option, ' ') ? escapeshellarg($option) : $option;
            };
        }

        return new Process(implode(' ', array_map($this->escapingAlgorithm, $options)));
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
    public static function getTempDir()
    {
        return rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
    }
}
