<?php namespace Assetic\Filter;

use Assetic\Filter\BaseNodeFilter;
use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Factory\AssetFactory;
use Assetic\Util\FilesystemUtils;
use Assetic\Contracts\Filter\DependencyExtractorInterface;

/**
 * Loads STYL files.
 *
 * @link http://learnboost.github.com/stylus/
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class StylusFilter extends BaseNodeFilter implements DependencyExtractorInterface
{
    /**
     * @var string Path to the binary for this process based filter
     */
    protected $binaryPath = '/usr/local/bin/stylus';

    /*
     * Filter Options
     */

    private $compress;

    /**
     * Enable output compression.
     *
     * @param boolean $compress
     */
    public function setCompress(bool $compress)
    {
        $this->compress = $compress;
    }

    /**
     * {@inheritDoc}
     */
    protected function getOutputPath()
    {
        $prefix = preg_replace('/[^\w]/', '', static::class);
        $path = FilesystemUtils::createThrowAwayDirectory($prefix) . '/output.css';
        touch($path);
        return $path;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        $args = [];

        if (null !== $this->compress && $this->compress) {
            $args[] = '--compress';
        }

        if ($dir = $asset->getSourceDirectory()) {
            $args[] = $dir;
        } else {
            $args[] = '{INPUT}';
        }

        $args[] = '--out';
        $args[] = '{OUTPUT}';

        // Run the filter
        $result = $this->runProcess($asset->getContent(), $args);

        $asset->setContent($result);
    }

    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        // todo
        return [];
    }
}
