<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;

/**
 * Compiles Handlebars templates into Javascript.
 *
 * @link http://handlebarsjs.com/
 * @link https://handlebarsjs.com/precompilation.html
 * @author Keyvan Akbary <keyvan@funddy.com>
 */
class HandlebarsFilter extends BaseNodeFilter
{
    /**
     * @var string Path to the binary for this process based filter
     */
    protected $binaryPath = '/usr/bin/handlebars';

    /*
     * Filter Options
     */

    /**
     * @var boolean Minimize the output
     */
    private $minimize = false;

    /**
     * @var boolean Output template function only
     */
    private $simple = false;

    public function setMinimize($minimize)
    {
        $this->minimize = $minimize;
    }

    public function setSimple($simple)
    {
        $this->simple = $simple;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $args = [
            '{INPUT}',
            '-f',
            '{OUTPUT}',
        ];

        if ($this->minimize) {
            $args[] = '--min';
        }

        if ($this->simple) {
            $args[] = '--simple';
        }

        $result = $this->runProcess($asset->getContent(), $args);
        $asset->setContent($result);
    }
}
