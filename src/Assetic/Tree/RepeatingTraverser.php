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

/**
 * Continues traversing until a no-op traversal is detected.
 */
class RepeatingTraverser implements TraverserInterface
{
    private $delegate;
    private $comparator;

    public function __construct(TraverserInterface $delegate, ComparatorInterface $comparator)
    {
        $this->delegate = $delegate;
        $this->comparator = $comparator;
    }

    public function addVisitor(VisitorInterface $visitor)
    {
        $this->delegate->addVisitor($visitor);
    }

    public function traverse(NodeInterface $node)
    {
        $before = clone $node;
        $after  = $this->delegate->traverse($node);

        return $this->comparator->compare($before, $after) ? $after : $this->traverse($after);
    }
}
