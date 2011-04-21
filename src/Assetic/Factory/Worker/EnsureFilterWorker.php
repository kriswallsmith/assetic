<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Worker;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

/**
 * Applies a filter to an asset based on a target URL match.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @todo A better asset-matcher mechanism
 */
class EnsureFilterWorker implements WorkerInterface
{
    private $pattern;
    private $filter;

    /**
     * Constructor.
     *
     * @param string          $pattern A regex for checking the asset's target URL
     * @param FilterInterface $filter  A filter to apply if the regex matches
     */
    public function __construct($pattern, FilterInterface $filter)
    {
        $this->pattern = $pattern;
        $this->filter = $filter;
    }

    public function process(AssetInterface $asset)
    {
        if (0 < preg_match($this->pattern, $asset->getTargetUrl())) {
            $asset->ensureFilter($this->filter);
        }

        return $asset;
    }
}
