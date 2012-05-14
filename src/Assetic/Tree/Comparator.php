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

class Comparator implements ComparatorInterface
{
    public function compare(NodeInterface $a, NodeInterface $b)
    {
        if ($a->getAttributes() != $b->getAttributes()) {
            return false;
        }

        $aChildren = $a->getChildren();
        $bChildren = $b->getChildren();

        if (count($aChildren) != count($bChildren) || array_keys($aChildren) != array_keys($bChildren)) {
            return false;
        }

        foreach ($aChildren as $i => $aChild) {
            if (!$this->compare($aChild, $bChildren[$i])) {
                return false;
            }
        }

        return true;
    }
}
