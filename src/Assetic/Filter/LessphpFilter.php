<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Filter\DependencyExtractorInterface;
use Assetic\Factory\AssetFactory;
use Assetic\Util\LessUtils;

/**
 * Loads LESS files using the PHP implementation of less, less.php.
 *
 * Less files are mostly compatible, but there are slight differences.
 *
 * @link https://github.com/wikimedia/less.php
 *
 * @author David Buchmann <david@liip.ch>
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Jack Wilkinson <me@jackwilky.com>
 */
class LessphpFilter extends BaseFilter implements DependencyExtractorInterface
{
    private $presets = [];
    private $formatter;
    private $options = [
        'compress' => true
    ];

    /**
     * Lessphp Load Paths
     *
     * @var array
     */
    protected $loadPaths = [];

    /**
     * Adds a load path to the paths used by lessphp
     *
     * @param string $path Load Path
     */
    public function addLoadPath($path)
    {
        $this->loadPaths[] = $path;
    }

    /**
     * Sets load paths used by lessphp
     *
     * @param array $loadPaths Load paths
     */
    public function setLoadPaths(array $loadPaths)
    {
        $this->loadPaths = $loadPaths;
    }

    public function setPresets(array $presets)
    {
        $this->presets = $presets;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param string $formatter One of "lessjs", "compressed", or "classic".
     */
    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $lc = new \lessc();
        if ($dir = $asset->getSourceDirectory()) {
            $lc->importDir = $dir;
        }

        foreach ($this->loadPaths as $loadPath) {
            $lc->addImportDir($loadPath);
        }

        if ($this->formatter) {
            $lc->setFormatter($this->formatter);
        }

        if (method_exists($lc, 'setOptions') && count($this->options) > 0) {
            $lc->setOptions($this->options);
        }

        $asset->setContent($lc->parse($asset->getContent(), $this->presets));
    }

    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        $loadPaths = $this->loadPaths;
        if (null !== $loadPath) {
            $loadPaths[] = $loadPath;
        }

        if (empty($loadPaths)) {
            return [];
        }

        $children = [];
        foreach (LessUtils::extractImports($content) as $reference) {
            if ('.css' === substr($reference, -4)) {
                // skip normal css imports
                // todo: skip imports with media queries
                continue;
            }

            if ('.less' !== substr($reference, -5)) {
                $reference .= '.less';
            }

            foreach ($loadPaths as $loadPath) {
                if (file_exists($file = $loadPath.'/'.$reference)) {
                    $coll = $factory->createAsset($file, [], array('root' => $loadPath));
                    foreach ($coll as $leaf) {
                        $leaf->ensureFilter($this);
                        $children[] = $leaf;
                        goto next_reference;
                    }
                }
            }

            next_reference:
        }

        return $children;
    }
}
