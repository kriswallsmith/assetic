<?php

namespace Assetic\Asset;

use Assetic\AssetManager;
use Assetic\Filter\FilterInterface;

/**
 * A reference to an asset in the asset manager.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AssetReference implements AssetInterface
{
    private $am;
    private $name;

    public function __construct(AssetManager $am, $name)
    {
        $this->am = $am;
        $this->name = $name;
    }

    public function ensureFilter(FilterInterface $filter)
    {
        $this->callAsset(__FUNCTION__, array($filter));
    }

    public function load()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function dump()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function getPath()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function setPath($path)
    {
        $this->callAsset(__FUNCTION__, array($path));
    }

    public function getBody()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function setBody($body)
    {
        $this->callAsset(__FUNCTION__, array($body));
    }

    private function callAsset($method, $arguments = array())
    {
        return call_user_func_array(array($this->am->get($this->name), $method), $arguments);
    }
}
