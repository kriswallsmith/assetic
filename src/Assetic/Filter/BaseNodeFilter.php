<?php namespace Assetic\Filter;

abstract class BaseNodeFilter extends BaseProcessFilter
{
    /**
     * @var string Path to the Node binary for this process based filter
     */
    protected $nodeBinaryPath;

    private $nodePaths = array();

    /**
     * Constructor
     *
     * @param string $binaryPath Path to the binary to use for this filter, overrides the default path
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

    protected function createProcess(array $arguments = array())
    {
        $pb = parent::createProcess($arguments);

        if ($this->nodePaths) {
            $this->mergeEnv($pb);
            $pb->setEnv(['NODE_PATH' => implode(PATH_SEPARATOR, $this->nodePaths)]);
        }

        return $pb;
    }
}
