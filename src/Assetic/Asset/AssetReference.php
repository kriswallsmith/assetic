<?php

namespace Assetic\Asset;

use Assetic\AssetManager;
use Assetic\Filter\FilterInterface;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    /**
     * Returns the referenced asset.
     *
     * @return AssetInterface The referenced asset
     */
    public function resolve()
    {
        return $this->am->get($this->name);
    }

    public function ensureFilter(FilterInterface $filter)
    {
        $this->callAsset(__FUNCTION__, array($filter));
    }

    public function getFilters()
    {
        $this->callAsset(__FUNCTION__);
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        return $this->callAsset(__FUNCTION__, array($additionalFilter));
    }

    public function dump(FilterInterface $additionalFilter = null)
    {
        return $this->callAsset(__FUNCTION__, array($additionalFilter));
    }

    public function getUrl()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function setUrl($url)
    {
        $this->callAsset(__FUNCTION__, array($url));
    }

    public function getBody()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function setBody($body)
    {
        $this->callAsset(__FUNCTION__, array($body));
    }

    public function getContext()
    {
        return $this->callAsset(__FUNCTION__);
    }

    public function setContext(AssetInterface $context = null)
    {
        $this->callAsset(__FUNCTION__, array($context));
    }

    // private

    private function callAsset($method, $arguments = array())
    {
        return call_user_func_array(array($this->resolve(), $method), $arguments);
    }
}
