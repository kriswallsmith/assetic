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
use MatthiasMullie\Minify\JS;
use MatthiasMullie\Minify\CSS;

/**
 * MatthiasMullie\Minify filter. Can be used to minify both CSS & JS files.
 *
 * @link https://github.com/matthiasmullie/minify
 * @link http://www.minifier.org
 * @author Matthias Mullie <minify@mullie.eu>
 */
class MMMinifyFilter implements FilterInterface
{
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $minifier = $this->getMinifier($asset);

        $minifier->add($asset->getSourceRoot().'/'.$asset->getSourcePath());
        $minified = $minifier->minify();

        $asset->setContent($minified);
    }

    /**
     * Returns the appropriate Minifier class for the given asset, be that CSS
     * or JS.
     *
     * We'll first detect based on file extension, which will likely always be
     * either js or css.
     *
     * In case the asset extension matched neither js or css, we'll "guess"
     * based on the content.
     * We'll look for very common JS keywords. Nearly every JS file, no
     * matter how simple, should have at least some of those (you'll need
     * them even for the simplest possible kind of logic.
     * CSS on the other hand is a pretty strict language and should'nt have
     * any of those words, except for maybe in comments. Let's first strip
     * whatever looks like a CSS comment, then look for the keywords.
     *
     * @param  AssetInterface $asset
     * @return CSS|JS
     */
    private function getMinifier(AssetInterface $asset)
    {
        // figure out if CSS or JS file based on file extension
        $path = $asset->getSourcePath();
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        if ($ext === 'js') {
            return new JS();
        } elseif ($ext === 'css') {
            return new CSS();
        }

        // invalid extension, guess based on content
        $content = $asset->getContent();
        $content = preg_replace('/\/\*.*?\*\//s', '', $content);
        $match = preg_match('/(^|\s|;)(function|var|for|do|while|if|else|new|switch|return)(\s|;|\{|\})/', $content);
        if ($match) {
            return new JS();
        } else {
            return new CSS();
        }
    }
}
