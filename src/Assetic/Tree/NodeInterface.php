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

interface NodeInterface
{
    function getAttribute($name, $default = null);
    function setAttribute($name, $value);
    function getAttributes();

    function getParent();
    function setParent(NodeInterface $parent = null);

    function getChildren();
    function setChildren(array $children);
    function addChildren(array $children);
    function setChild($key, NodeInterface $child);
    function removeChild(NodeInterface $child);
}
