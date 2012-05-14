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

interface VisitorInterface
{
    /**
     * Called when the traverser enters a node.
     *
     * @return NodeInterface The same node or a replacement
     */
    function enter(NodeInterface $node);

    /**
     * Called when the traverser leaves a node.
     *
     * @return NodeInterface|null The node, a replacement, or null to remove
     */
    function leave(NodeInterface $node);

    /**
     * Returns the current visitor's priority.
     *
     * @return integer The priority
     */
    function getPriority();
}
