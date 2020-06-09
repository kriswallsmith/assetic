<?php namespace Assetic\Filter;

abstract class BaseNodeFilter extends BaseProcessFilter
{
    /**
     * @var string Path to the Node binary for this process based filter
     */
    protected $nodeBinaryPath;

    private $nodePaths = [];

    /**
     * Constructor
     *
     * @param string $binaryPath Path to the binary to use for this filter, overrides the default path
     * @param mixed $nodeBinaryPath
     */
    public function __construct($binaryPath = '', $nodeBinaryPath = null)
    {
        if (!empty($nodeBinaryPath)) {
            $this->nodeBinaryPath = $nodeBinaryPath;
        }

        parent::__construct($binaryPath);
    }

    /**
     * Get the arguments to be passed to the process regarding the process path
     *
     * @return array
     */
    protected function getPathArgs()
    {
        return $this->nodeBinaryPath
            ? [$this->nodeBinaryPath, $this->binaryPath]
            : [$this->binaryPath];
    }

    /**
     * {@inheritDoc}
     */
    protected function runProcess(string $input, array $arguments = [])
    {
        try {
            $result = parent::runProcess($input, $arguments);
        } catch (\Exception $e) {
            $this->cleanUp();
            if ($this->processReturnCode === 127) {
                throw new \RuntimeException('Path to node executable could not be resolved.');
            } else {
                throw $e;
            }
        }

        return $result;
    }

    public function getNodePaths()
    {
        return $this->nodePaths;
    }

    public function setNodePaths(array $nodePaths)
    {
        $this->nodePaths = $nodePaths;
    }

    public function addNodePath($nodePath)
    {
        $this->nodePaths[] = $nodePath;
    }

    protected function createProcess(array $arguments = [])
    {
        $pb = parent::createProcess($arguments);

        if ($this->nodePaths) {
            $this->mergeEnv($pb);
            $pb->setEnv(['NODE_PATH' => implode(PATH_SEPARATOR, $this->nodePaths)]);
        }

        return $pb;
    }
}
