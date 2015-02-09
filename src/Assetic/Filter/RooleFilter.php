<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Factory\AssetFactory;
use Assetic\Util\FilesystemUtils;

/**
 * Loads Roole files.
 *
 * @link http://roole.org
 * @author Marcin Chwedziak <tiraeth@gmail.com>
 */
class RooleFilter extends BaseNodeFilter implements DependencyExtractorInterface
{
    private $rooleBin;
    private $nodeBin;

    /**
     * Constructor
     *
     * @param string $rooleBin The path to the roole binary
     * @param string $nodeBin  The path to the node binary
     */
    public function __construct($rooleBin = '/usr/bin/roole', $nodeBin = null)
    {
        $this->rooleBin = $rooleBin;
        $this->nodeBin = $nodeBin;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $input = FilesystemUtils::createTemporaryFile('roole');
        $output = $input.'.css';

        file_put_contents($input, $asset->getContent());

        $pb = $this->createProcessBuilder($this->nodeBin
            ? array($this->nodeBin, $this->rooleBin)
            : array($this->rooleBin));

        $pb->add($input);

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 !== $code) {
            if (file_exists($output)) {
                unlink($output);
            }

            throw FilterException::fromProcess($proc);
        }

        if (!file_exists($output)) {
            throw new \RuntimeException('Error creating output file.');
        }

        $asset->setContent(file_get_contents($output));
        unlink($output);
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        // todo
        return array();
    }
}
