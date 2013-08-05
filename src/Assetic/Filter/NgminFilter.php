<?php
/**
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
use Assetic\Filter\BaseNodeFilter;

class NgminFilter extends BaseNodeFilter
{

    protected $ngminBin;
    protected $nodeBin;

    /**
     * @param string $ngminBin Path to ngmin executable
     * @param string $nodeBin Path to node.js executable
     */
    public function __construct($ngminBin = '/usr/bin/ngmin', $nodeBin = null)
    {
        $this->ngminBin = $ngminBin;
        $this->nodeBin = $nodeBin;
    }


    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        $pb = $this->createProcessBuilder($this->nodeBin
                ? array($this->nodeBin, $this->ngminBin)
                : array($this->ngminBin));

        // input and output files
        $input = tempnam(sys_get_temp_dir(), 'input');
        $output = tempnam(sys_get_temp_dir(), 'output');

        file_put_contents($input, $asset->getContent());
        $pb->add($input)->add($output);

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 !== $code) {
            if (file_exists($output)) {
                unlink($output);
            }

            if (127 === $code) {
                throw new \RuntimeException('Path to node executable could not be resolved.');
            }

            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        if (!file_exists($output)) {
            throw new \RuntimeException('Error creating output file.');
        }

        $asset->setContent(file_get_contents($output));

        unlink($output);
    }
}