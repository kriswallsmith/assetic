<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Contracts\Filter\DependencyExtractorInterface;
use Assetic\Factory\AssetFactory;
use CssEmbed\CssEmbed;

/**
 * A filter that embed url directly into css
 *
 * @author Pierre Tachoire <pierre.tachoire@gmail.com>
 * @link https://github.com/krichprollsch/phpCssEmbed
 */
class PhpCssEmbedFilter extends BaseFilter implements DependencyExtractorInterface
{
    private $presets = [];

    public function setPresets(array $presets)
    {
        $this->presets = $presets;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $pce = new CssEmbed();
        if ($dir = $asset->getSourceDirectory()) {
            $pce->setRootDir($dir);
        }

        $asset->setContent($pce->embedString($asset->getContent()));
    }

    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        // todo
        return [];
    }
}
