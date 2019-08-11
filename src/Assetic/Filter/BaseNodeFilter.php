<?php namespace Assetic\Filter;

abstract class BaseNodeFilter extends BaseProcessFilter
{
    private $nodePaths = array();

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
