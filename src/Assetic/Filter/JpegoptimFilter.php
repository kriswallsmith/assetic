<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Exception\FilterException;

/**
 * Runs assets through Jpegoptim.
 *
 * @link   http://www.kokkonen.net/tjko/projects.html
 * @link   https://www.systutorials.com/docs/linux/man/1-jpegoptim/
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class JpegoptimFilter extends BaseProcessFilter
{
    /**
     * @var string Path to the binary for this process based filter
     */
    protected $binaryPath = '/usr/bin/jpegoptim';

    /**
     * @var bool Flag to indicate that the process will output the result to the input file
     */
    protected $useInputAsOutput = true;

    /*
     * Filter Options
     */

    /**
     * @var boolean Strip all markers from the output
     */
    private $stripAll;

    /**
     * @var integer Maximum image quality factor
     */
    private $max;

    public function setStripAll($stripAll)
    {
        $this->stripAll = $stripAll;
    }

    public function setMax($max)
    {
        $this->max = $max;
    }

    public function filterDump(AssetInterface $asset)
    {
        $args = [];

        if ($this->stripAll) {
            $args[] = '--strip-all';
        }

        if ($this->max) {
            $args[] = '--max=' . $this->max;
        }

        $args[] = '{INPUT}';

        // Run the filter
        $result = $this->runProcess($asset->getContent(), $args);

        // Check for any issues
        if (strpos($result, 'ERROR') !== false) {
            throw FilterException::fromProcess($this->getProcess())->setInput($asset->getContent());
        }

        $asset->setContent($result);
    }
}
