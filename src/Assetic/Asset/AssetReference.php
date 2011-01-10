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
        $this->am->get($this->name)->ensureFilter($filter);
    }

    public function load()
    {
        $this->am->get($this->name)->load();
    }

    public function dump()
    {
        return $this->am->get($this->name)->dump();
    }

    public function getPath()
    {
        return $this->am->get($this->name)->getPath();
    }

    public function setPath($path)
    {
        $this->am->get($this->name)->setPath($path);
    }

    public function getBody()
    {
        return $this->am->get($this->name)->getBody();
    }

    public function setBody($body)
    {
        $this->am->get($this->name)->setBody($body);
    }
}
