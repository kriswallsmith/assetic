<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;

/**
 * UglifyCss filter.
 *
 * @link https://github.com/fmarcia/UglifyCSS
 * @author Franck Marcia <franck.marcia@gmail.com>
 */
class UglifyCssFilter extends BaseNodeFilter
{
    /**
     * @var string Path to the binary for this process based filter
     */
    protected $binaryPath = '/usr/bin/uglifycss';

    /*
     * Filter Options
     */

    private $expandVars;
    private $uglyComments;
    private $cuteComments;

    /**
     * Expand variables
     * @param bool $expandVars True to enable
     */
    public function setExpandVars($expandVars)
    {
        $this->expandVars = $expandVars;
    }

    /**
     * Remove newlines within preserved comments
     * @param bool $uglyComments True to enable
     */
    public function setUglyComments($uglyComments)
    {
        $this->uglyComments = $uglyComments;
    }

    /**
     * Preserve newlines within and around preserved comments
     * @param bool $cuteComments True to enable
     */
    public function setCuteComments($cuteComments)
    {
        $this->cuteComments = $cuteComments;
    }

    /**
     * {@inheritDoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        $args = [];

        if ($this->expandVars) {
            $args[] = '--expand-vars';
        }

        if ($this->uglyComments) {
            $args[] = '--ugly-comments';
        }

        if ($this->cuteComments) {
            $args[] = '--cute-comments';
        }

        $args[] = '{INPUT}';

        $result = $this->runProcess($asset->getContent(), $args);
        $asset->setContent($result);
    }
}
