<?php namespace Assetic\Filter;

use Assetic\Util\FilesystemUtils;
use Assetic\Exception\FilterException;
use Symfony\Component\Process\Process;

/**
 * An external process based filter which provides a way to set a timeout on the process.
 */
abstract class BaseProcessFilter extends BaseFilter
{
    /**
     * @var string Path to the binary for this process based filter
     */
    protected $binaryPath;

    /**
     * @var boolean Flag to indicate that the process will output the result to the input file
     */
    protected $useInputAsOutput = false;

    /**
     * @var boolean Flag to indicate that the process will output the result to the output path instead of the process output
     */
    protected $outputToFile = false;

    /**
     * @var string Path to the process input
     */
    protected $inputPath;

    /**
     * @var string Path to the process output
     */
    protected $outputPath;

    protected $debug = false;

    /**
     * @var integer Seconds until the process is considered to have timed out
     */
    private $timeout;

    /**
     * @var Process The initialized process object
     */
    private $process;

    /**
     * @var integer The return code from the completed process
     */
    protected $processReturnCode;

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
     * Prepare the input and return the path to be used for {INPUT}
     *
     * @param string $input
     * @return string
     */
    protected function getInputPath(string $input)
    {
        $prefix = preg_replace('/[^\w]/', '', static::class);
        return FilesystemUtils::createTemporaryFile($prefix . '-input', $input);
    }

    /**
     * Prepare the output and return the path to be used for {OUTPUT}
     *
     * @return string
     */
    protected function getOutputPath()
    {
        $prefix = preg_replace('/[^\w]/', '', static::class);
        return FilesystemUtils::createTemporaryFile($prefix . '-output');
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
        // Set the binary path
        $args = $this->getPathArgs();

        if (empty($args)) {
            throw new \Exception('The binary path for ' . static::class . ' has not been set. Please set it and try again.');
        }

        // Prepare the input & output file paths
        $this->inputPath = $this->getInputPath($input);
        $this->outputPath = $this->getOutputPath();

        // Process the input and output argument locations
        foreach ($arguments as &$arg) {
            if (is_string($arg)) {
                $arg = str_replace('{INPUT}', $this->inputPath, $arg);

                // Only some processes output to file, others just use $process->getOutput()
                if (strpos($arg, '{OUTPUT}') !== false) {
                    $arg = str_replace('{OUTPUT}', $this->outputPath, $arg);
                    $this->outputToFile = true;
                }
            }
        }

        $args = array_merge($args, $arguments);

        $this->debug($args);

        // Run the process
        $process = $this->createProcess($args);
        $this->processReturnCode = $process->run();

        // Handle any errors
        if ($this->processReturnCode !== 0) {
            $this->cleanUp();
            throw FilterException::fromProcess($process)->setInput($input);
        }

        // Retrieve the output
        $output = $this->getOutput();

        // Check for errors
        if (strpos($output, 'Error: ') !== false) {
            $this->cleanUp();
            throw FilterException::fromProcess($this->getProcess())->setInput($input);
        }

        // Cleanup after ourselves
        $this->cleanUp();

        // Return the final result
        return $output;
    }

    /**
     * Retrieve the output from the process
     *
     * @return string
     */
    protected function getOutput()
    {
        $ouput = null;

        if ($this->useInputAsOutput) {
            $output = file_get_contents($this->inputPath);
        } elseif ($this->outputToFile) {
            $output = file_get_contents($this->outputPath);
        } else {
            $output = $this->getProcess()->getOutput();
        }

        return $output;
    }

    /**
     * Clean up after the process
     */
    protected function cleanUp()
    {
        if (file_exists($this->inputPath)) {
            if (is_dir($this->inputPath)) {
                FilesystemUtils::removeDirectory($this->inputPath);
            } else {
                unlink($this->inputPath);
            }
        }

        if (file_exists($this->outputPath)) {
            if (is_dir($this->outputPath)) {
                FilesystemUtils::removeDirectory($this->outputPath);
            } else {
                unlink($this->outputPath);
            }
        }
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

    protected function debug($args)
    {
        if ($this->debug) {
            var_dump($args); die;
        }
    }
}
