<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;

/**
 * UglifyJs2 filter.
 *
 * @link http://lisperator.net/uglifyjs
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class UglifyJs2Filter extends BaseNodeFilter
{
    private $uglifyjsBin;
    private $nodeBin;
    private $compress;
    private $beautify;
    private $mangle;
    private $screwIe8;
    private $comments;
    private $wrap;
    private $defines;

    public function __construct($uglifyjsBin = '/usr/bin/uglifyjs', $nodeBin = null)
    {
        $this->uglifyjsBin = $uglifyjsBin;
        $this->nodeBin = $nodeBin;
    }

    public function setCompress($compress)
    {
        $this->compress = $compress;
    }

    public function setBeautify($beautify)
    {
        $this->beautify = $beautify;
    }

    public function setMangle($mangle)
    {
        $this->mangle = $mangle;
    }

    public function setScrewIe8($screwIe8)
    {
        $this->screwIe8 = $screwIe8;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    public function setWrap($wrap)
    {
        $this->wrap = $wrap;
    }

    public function setDefines(array $defines)
    {
        $this->defines = $defines;
    }

    public function filterDump(AssetInterface $asset)
    {
        $args = $this->nodeBin
            ? array($this->nodeBin, $this->uglifyjsBin)
            : array($this->uglifyjsBin);

        if ($this->compress) {
            $args[] = '--compress';

            if (is_string($this->compress) && !empty($this->compress)) {
                $args[] = $this->compress;
            }
        }

        if ($this->beautify) {
            $args[] = '--beautify';
        }

        if ($this->mangle) {
            $args[] = '--mangle';
        }

        if ($this->screwIe8) {
            $args[] = '--screw-ie8';
        }

        if ($this->comments) {
            $args[] = '--comments';
            $args[] = true === $this->comments ? 'all' : $this->comments;
        }

        if ($this->wrap) {
            $args[] = '--wrap';
            $args[] = $this->wrap;
        }

        if ($this->defines) {
            $args[] = '--define';
            $args[] = implode(',', $this->defines);
        }

        // input and output files
        $input  = FilesystemUtils::createTemporaryFile('uglifyjs2_in', $asset->getContent());
        $output = FilesystemUtils::createTemporaryFile('uglifyjs2_out');

        $args[] = '-o';
        $args[] = $output;
        $args[] = $input;

        $process = $this->createProcess($args);

        $code = $process->run();
        unlink($input);

        if (0 !== $code) {
            if (file_exists($output)) {
                unlink($output);
            }

            if (127 === $code) {
                throw new \RuntimeException('Path to node executable could not be resolved.');
            }

            throw FilterException::fromProcess($process)->setInput($asset->getContent());
        }

        if (!file_exists($output)) {
            throw new \RuntimeException('Error creating output file.');
        }

        $asset->setContent(file_get_contents($output));

        unlink($output);
    }
}
