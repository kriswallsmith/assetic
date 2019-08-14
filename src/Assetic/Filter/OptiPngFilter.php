<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;

/**
 * Runs assets through OptiPNG.
 *
 * @link   http://optipng.sourceforge.net/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class OptiPngFilter extends BaseProcessFilter
{
    /**
     * @var string Path to the binary for this process based filter
     */
    protected $binaryPath = '/usr/bin/optipng';

    /**
     * @var boolean Flag to indicate that the output file should not exist before the process is run
     */
    protected $deleteOutputFile = true;

    /*
     * Filter Options
     */

    private $level;

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $args = [];

        if (!is_null($this->level)) {
            $args[] = '-o';
            $args[] = $this->level;
        }

        $args[] = '-out';
        $args[] = '{OUTPUT}';
        $args[] = '{INPUT}';

        // Run the filter
        $result = $this->runProcess($asset->getContent(), $args);

        $asset->setContent($result);
    }
}
