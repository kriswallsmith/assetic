<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
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
    private $debug;

    /**
     * Constructor.
     *
     * @param string          $pattern A regex for checking the asset's target URL
     * @param FilterInterface $filter  A filter to apply if the regex matches
     * @param Boolean         $debug   The debug mode to check for
     */
    public function __construct($pattern, FilterInterface $filter, $debug = null)
    {
        $this->pattern = $pattern;
        $this->filter = $filter;
        $this->debug = $debug;
    }

    /**
     * Processes an asset.
     *
     * @param AssetInterface $asset An asset
     * @param Boolean        $debug Debug mode
     */
    public function process(AssetInterface $asset, $debug = false)
    {
        if ((null === $this->debug || $this->debug === $debug) && preg_match($this->pattern, $asset->getTargetUrl())) {
            $asset->ensureFilter($this->filter);
        }
    }
}
