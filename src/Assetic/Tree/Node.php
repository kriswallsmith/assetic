<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Tree;

class Node implements NodeInterface
{
    private $attributes;
    private $children;

    public function __construct(array $attributes = array())
    {
        $this->attributes = $attributes;
        $this->children = array();
    }

    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
    }

    public function setAttribute($name, $value)
    {
        if (!$this->isAttributeValid($value)) {
            throw new \InvalidArgumentException('Attributes may only be composed of arrays and scalars');
        }

        $this->attributes[$name] = $value;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(NodeInterface $parent = null)
    {
        $this->parent = $parent;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren(array $children)
    {
        // disconnect current children
        foreach ($this->children as $child) {
            $child->setParent(null);
        }

        $this->children = array();
        $this->addChildren($children);
    }

    public function addChildren(array $children)
    {
        foreach ($children as $key => $child) {
            $this->setChild($key, $child);
        }
    }

    public function setChild($key, NodeInterface $child)
    {
        $child->setParent($this);
        $this->children[$key] = $child;
    }

    public function removeChild(NodeInterface $child)
    {
        if (false !== $key = array_search($child, $this->children, true)) {
            $this->children[$key]->setParent(null);
            unset($this->children[$key]);
        }
    }

    /**
     * Only arrays and scalars are allowed.
     */
    private function isAttributeValid($value)
    {
        if (is_scalar($value)) {
            return true;
        }

        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $v) {
            if (!$this->isAttributeValid($v)) {
                return false;
            }
        }

        return true;
    }
}
