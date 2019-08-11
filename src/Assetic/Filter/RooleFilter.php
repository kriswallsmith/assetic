<?php namespace Assetic\Filter;

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
        $input = FilesystemUtils::createTemporaryFileAndWrite('roole', $asset->getContent());
        $output = $input.'.css';

        $args = $this->nodeBin
            ? array($this->nodeBin, $this->rooleBin)
            : array($this->rooleBin);


        $args[] = $input;

        $process = $this->createProcess($args);

        $code = $process->run();
        unlink($input);

        if (0 !== $code) {
            if (file_exists($output)) {
                unlink($output);
            }

            throw FilterException::fromProcess($process);
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
