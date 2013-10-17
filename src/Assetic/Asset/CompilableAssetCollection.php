<?php

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
class CompilableAssetCollection
    extends AssetCollection
{
    public function dump(FilterInterface $additionalFilter = null)
    {
        $this->setContent(parent::dump());

        $additionalFilter->filterDump($this);

        return $this->getContent();
    }
}
