<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Tree;

class Traverser implements TraverserInterface
{
    private $visitors;

    public function __construct()
    {
        $this->visitors = array();
    }

    public function addVisitor(VisitorInterface $visitor)
    {
        $priority = $visitor->getPriority();

        if (!isset($this->visitors[$priority])) {
            $this->visitors[$priority] = array();
            krsort($this->visitors);
        }

        if (!in_array($visitor, $this->visitors[$priority])) {
            $this->visitors[$priority][] = $visitor;
        }
    }

    public function traverse(NodeInterface $node, $recursion = 0)
    {
        foreach ($this->getVisitors() as $visitor) {
            $node = $this->traverseForVisitor($visitor, $node);
        }

        return $node;
    }

    private function getVisitors()
    {
        return $this->visitors ? call_user_func_array('array_merge', $this->visitors) : array();
    }

    private function traverseForVisitor(VisitorInterface $visitor, NodeInterface $node)
    {
        $node = $visitor->enter($node);

        foreach ($node->getChildren() as $key => $original) {
            $modified = $this->traverseForVisitor($visitor, $original);

            if ($modified instanceof NodeInterface) {
                $node->setChild($key, $modified);
            } else {
                $node->removeChild($original);
            }
        }

        return $visitor->leave($node);
    }
}
