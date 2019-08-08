<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Util\FilesystemUtils;

/**
 * Parses CSS and adds vendor prefixes to rules using values from the Can I Use website
 *
 * @link https://github.com/ai/autoprefixer
 * @author Alex Vasilenko <aa.vasilenko@gmail.com>
 */
class AutoprefixerFilter extends BaseNodeFilter
{
    /**
     * @var string
     */
    private $autoprefixerBin;

    /**
     * @var array
     */
    private $browsers = array();

    public function __construct($autoprefixerBin)
    {
        $this->autoprefixerBin = $autoprefixerBin;
    }

    /**
     * @param array $browsers
     */
    public function setBrowsers(array $browsers)
    {
        $this->browsers = $browsers;
    }

    /**
     * @param string $browser
     */
    public function addBrowser($browser)
    {
        $this->browsers[] = $browser;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $input = $asset->getContent();

        $args = [$this->autoprefixerBin];

        $output = FilesystemUtils::createTemporaryFile('autoprefixer');
        $args[] = '-o';
        $args[] = $output;

        if ($this->browsers) {
            $args[] = '-b';
            $args[] = implode(',', $this->browsers);
        }

        $process = $this->createProcessBuilder($args);

        $process->setInput($input);

        if (0 !== $process->run()) {
            throw FilterException::fromProcess($process)->setInput($asset->getContent());
        }

        $asset->setContent(file_get_contents($output));
        unlink($output);
    }

    /**
     * Filters an asset just before it's dumped.
     *
     * @param AssetInterface $asset An asset
     */
    public function filterDump(AssetInterface $asset)
    {
    }
}
