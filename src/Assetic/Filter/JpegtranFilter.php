<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;

/**
 * Runs assets through jpegtran.
 *
 * @link http://jpegclub.org/jpegtran/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class JpegtranFilter extends BaseProcessFilter
{
    /**
     * @var string Path to the binary for this process based filter
     */
    protected $binaryPath = '/usr/bin/jpegtran';

    /*
     * Filter Options
     */

    private $optimize;
    private $copy;
    private $progressive;
    private $restart;

    public function setOptimize($optimize)
    {
        $this->optimize = $optimize;
    }

    public function setCopy($copy)
    {
        $this->copy = $copy;
    }

    public function setProgressive($progressive)
    {
        $this->progressive = $progressive;
    }

    public function setRestart($restart)
    {
        $this->restart = $restart;
    }

    public function filterDump(AssetInterface $asset)
    {
        $args = [];

        if ($this->optimize) {
            $args[] = '-optimize';
        }

        if ($this->copy) {
            $args[] = '-copy';
            $args[] = $this->copy;
        }

        if ($this->progressive) {
            $args[] = '-progressive';
        }

        if (!is_null($this->restart)) {
            $args[] = '-restart';
            $args[] = $this->restart;
        }

        $args[] = '{INPUT}';

        // Run the filter
        $result = $this->runProcess($asset->getContent(), $args);

        $asset->setContent($result);
    }
}
