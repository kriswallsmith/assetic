<?php namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
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
    private $optipngBin;
    private $level;

    /**
     * Constructor.
     *
     * @param string $optipngBin Path to the optipng binary
     */
    public function __construct($optipngBin = '/usr/bin/optipng')
    {
        $this->optipngBin = $optipngBin;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $args = [$this->optipngBin];

        if (null !== $this->level) {
            $args[] = '-o';
            $args[] = $this->level;
        }

        $args[] = '-out';
        $args[] = $output = FilesystemUtils::createTemporaryFile('optipng_out');
        unlink($output);

        $args[] = $input = FilesystemUtils::createTemporaryFileAndWrite('optinpg_in', $asset->getContent());

        $process = $this->createProcess($args);
        $code = $process->run();

        if (0 !== $code) {
            unlink($input);
            throw FilterException::fromProcess($process)->setInput($asset->getContent());
        }

        $asset->setContent(file_get_contents($output));

        unlink($input);
        unlink($output);
    }
}
