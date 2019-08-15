<?php namespace Assetic\Filter;

use Assetic\Util\FilesystemUtils;
use Assetic\Exception\FilterException;
use Symfony\Component\Process\Process;
use Assetic\Contracts\Filter\FilterInterface;

/**
 * An external process based filter which provides a way to set a timeout on the process.
 */
abstract class BaseProcessFilter implements FilterInterface
{
    /**
     * @var string Path to the binary for this process based filter
     */
    protected $binaryPath;

    /**
     * @var bool Flag to indicate that the process will output the result to the input file
     */
    protected $useInputAsOutput = false;

    /**
     * @var integer Seconds until the process is considered to have timed out
     */
    private $timeout;

    /**
     * @var Process The initialized process object
     */
    private $process;

    /**
     * Constructor
     *
     * @param string $binaryPath Path to the binary to use for this filter, overrides the default path
     */
    public function __construct($binaryPath = '')
    {
        if (!empty($binaryPath)) {
            $this->binaryPath = $binaryPath;
        }
    }

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
     * Creates a new process.
     *
     * @param array $arguments An optional array of arguments
     * @return Process A new Process object
     */
    protected function createProcess(array $arguments = [])
    {
        $process = new Process($arguments);

        if (null !== $this->timeout) {
            $process->setTimeout($this->timeout);
        }

        return $this->process = $process;
    }

    /**
     * Retrieves the process
     *
     * @return Process|null
     */
    protected function getProcess()
    {
        return $this->process;
    }

    /**
     * Runs a process with the provided argument and returns the output
     *
     * @param string $input The input to provide the process
     * @param array $arguments The arguments to provide the process
     * @return string The ouput created by the process
     * @throws FilterException
     * @throws Exception
     */
    protected function runProcess(string $input, array $arguments = [])
    {
        if (empty($this->binaryPath)) {
            throw new \Exception('The binaryPath for ' . static::class . ' has not been set. Please set it and try again.');
        }

        // Set the binary path
        $args = $this->getPathArgs();

        // Prepare the input & output file paths
        $prefix = preg_replace('/[^\w]/', '', static::class);
        $inputFile = FilesystemUtils::createTemporaryFile($prefix . '-input', $input);
        $outputFile = FilesystemUtils::createTemporaryFile($prefix . '-output');
        $outputToFile = false;

        // Process the input and output argument locations
        foreach ($arguments as &$arg) {
            if (is_string($arg)) {
                $arg = str_replace('{INPUT}', $inputFile, $arg);

                // Only some processes output to file, others just use $process->getOutput()
                if (strpos($arg, '{OUTPUT}') !== false) {
                    $arg = str_replace('{OUTPUT}', $outputFile, $arg);
                    $outputToFile = true;
                }
            }
        }

        $args = array_merge($args, $arguments);

        // Run the process
        $process = $this->createProcess($args);
        $code = $process->run();

        // Handle any errors
        if (0 !== $code) {
            unlink($inputFile);
            unlink($outputFile);
            throw FilterException::fromProcess($process)->setInput($input);
        }

        // Retrieve the output
        if ($this->useInputAsOutput) {
            $output = file_get_contents($inputFile);
        } elseif ($outputToFile) {
            $output = file_get_contents($outputFile);
        } else {
            $output = $process->getOutput();
        }

        // Cleanup after ourselves
        unlink($inputFile);
        unlink($outputFile);

        // Return the final result
        return $output;
    }

    /**
     * Get the arguments to be passed to the process regarding the process path
     *
     * @return array
     */
    protected function getPathArgs()
    {
        return [$this->binaryPath];
    }

    protected function mergeEnv(Process $process)
    {
        foreach (array_filter($_SERVER, 'is_scalar') as $key => $value) {
            $process->setEnv([$key => $value]);
        }
    }
}
