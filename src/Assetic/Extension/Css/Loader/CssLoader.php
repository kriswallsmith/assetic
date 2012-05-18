<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Css\Loader;

use Assetic\Asset\AbstractAssetVisitor;
use Assetic\Asset\AssetInterface;
use Assetic\Asset\FactoryInterface;

/**
 * The CSS loader loads children from CSS assets.
 */
class CssLoader extends AbstractAssetVisitor
{
    const REGEX_COMMENT = '/\/\*.*?\*\//s';
    const REGEX_URL     = '/url\((["\']?)(?<url>.*?)(\\1)\)/';
    const REGEX_IMPORT  = '/@import (?!url\()(\'|"|)(?<url>[^\'"\)\n\r]*)\1;?/';

    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;

        parent::__construct();
    }

    protected function enterAsset(AssetInterface $asset)
    {
        if ($asset->getAttribute('css.loaded') || !$content = $asset->getAttribute('content')) {
            return $asset;
        }

        $types = $asset->getAttribute('types', array());
        if ('css' !== array_pop($types)) {
            return $asset;
        }

        $asset->setAttribute('css.loaded', true);
        $asset->addChildren($this->loadChildren($content));

        return $asset;
    }

    private function loadChildren($content)
    {
        $lines  = array();
        $assets = array();

        // remove comments
        $clean = preg_replace(self::REGEX_COMMENT, '', $content);

        foreach (array(self::REGEX_URL, self::REGEX_IMPORT) as $pattern) {
            $seen = array();
            preg_match_all($pattern, $clean, $matches);
            foreach ($matches['url'] as $i => $url) {
                $fragment = $matches[0][$i];
                $lines[]  = $line = $this->determineLineNumber($content, $fragment, $seen);
                $assets[] = $this->factory->createAsset(array(
                    'parent.rev_path' => $url,      // the path used by the parent
                    'parent.fragment' => $fragment, // the fragment from the parent
                    'parent.line'     => $line,     // the parent line number
                ));
            }
        }

        array_multisort($lines, $assets);

        return $assets;
    }

    private function determineLineNumber($content, $fragment, array & $seen = array())
    {
        if (!isset($seen[$fragment])) {
            $seen[$fragment] = 0;
        }

        $chunks = explode($fragment, $content);
        $before = implode($fragment, array_slice($chunks, 0, ++$seen[$fragment]));

        return substr_count($before, "\n") + 1;
    }
}
