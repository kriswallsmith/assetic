<?php namespace Assetic\Factory\Worker;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Factory\AssetFactory;
use Assetic\Contracts\Filter\FilterInterface;
use Assetic\Contracts\Factory\Worker\WorkerInterface;

/**
 * Applies a filter to an asset based on a source and/or target path match.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @todo A better asset-matcher mechanism
 */
class EnsureFilterWorker implements WorkerInterface
{
    const CHECK_SOURCE = 1;
    const CHECK_TARGET = 2;

    private $pattern;
    private $filter;
    private $flags;

    /**
     * Constructor.
     *
     * @param string          $pattern A regex for checking the asset's target URL
     * @param FilterInterface $filter  A filter to apply if the regex matches
     * @param integer         $flags   Flags for what to check
     */
    public function __construct($pattern, FilterInterface $filter, $flags = null)
    {
        if (null === $flags) {
            $flags = self::CHECK_SOURCE | self::CHECK_TARGET;
        }

        $this->pattern = $pattern;
        $this->filter = $filter;
        $this->flags = $flags;
    }

    public function process(AssetInterface $asset, AssetFactory $factory)
    {
        if (
            (self::CHECK_SOURCE === (self::CHECK_SOURCE & $this->flags) && preg_match($this->pattern, $asset->getSourcePath()))
            ||
            (self::CHECK_TARGET === (self::CHECK_TARGET & $this->flags) && preg_match($this->pattern, $asset->getTargetPath()))
        ) {
            $asset->ensureFilter($this->filter);
        }
    }
}
