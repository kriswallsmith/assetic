<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset;

use Assetic\Filter\FilterInterface;

/**
 * An asset collection designed to be processed by filters.
 *
 * This asset collection is useful for global minification and/or global compilation,
 * the filters are not transferred to child nodes.
 *
 * @author Grégory PLANCHAT <g.planchat@gmail.com>
 */
class CompilableAssetCollection extends AssetCollection
{
    public function dump(FilterInterface $additionalFilter = null)
    {
        $this->setContent(parent::dump());

        $additionalFilter->filterDump($this);

        return $this->getContent();
    }
}
