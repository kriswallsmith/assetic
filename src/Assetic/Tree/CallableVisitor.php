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

class CallableVisitor implements VisitorInterface
{
    private $enterCallable;
    private $leaveCallable;

    public function __construct($enterCallable = null, $leaveCallable = null)
    {
        if ($enterCallable && !is_callable($enterCallable)) {
            throw new \InvalidArgumentException('The enter callable is not callable');
        }

        if ($leaveCallable && !is_callable($leaveCallable)) {
            throw new \InvalidArgumentException('The leave callable is not callable');
        }

        $this->enterCallable = $enterCallable;
        $this->leaveCallable = $leaveCallable;
    }

    public function enter(NodeInterface $node)
    {
        return $this->enterCallable ? call_user_func($this->enterCallable, $node) : $node;
    }

    public function leave(NodeInterface $node)
    {
        return $this->leaveCallable ? call_user_func($this->leaveCallable, $node) : $node;
    }

    public function getPriority()
    {
        return 0;
    }
}
