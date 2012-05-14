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

interface TraverserInterface
{
    /**
     * Adds a visitor to the traverser.
     */
    function addVisitor(VisitorInterface $visitor);

    /**
     * Traverses the supplied node.
     *
     * @return NodeInterface The modified or replaced node
     */
    function traverse(NodeInterface $node);
}
