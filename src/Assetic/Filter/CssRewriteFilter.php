<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Fixes relative CSS urls.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CssRewriteFilter implements FilterInterface
{
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $sourceUrl = $asset->getSourceUrl();
        $targetUrl = $asset->getTargetUrl();

        if (null === $sourceUrl || null === $targetUrl || $sourceUrl == $targetUrl) {
            return;
        }

        // learn how to get from the target back to the source
        if (false !== strpos($sourceUrl, '://')) {
            // the source is absolute, this should be easy
            $parts = parse_url($sourceUrl);

            $host = $parts['scheme'].'://'.$parts['host'];
            $path = dirname($parts['path']).'/';
        } else {
            // assume source and target are on the same host
            $host = '';

            // pop entries off the target until it fits in the source
            if ('.' == dirname($sourceUrl)) {
                $path = str_repeat('../', substr_count($targetUrl, '/'));
            } elseif ('.' == $targetDir = dirname($targetUrl)) {
                $path = dirname($sourceUrl).'/';
            } else {
                $path = '';
                while (0 !== strpos($sourceUrl, $targetDir)) {
                    if (false !== $pos = strrpos($targetDir, '/')) {
                        $targetDir = substr($targetDir, 0, $pos);
                        $path .= '../';
                    } else {
                        $targetDir = '';
                        $path .= '../';
                        break;
                    }
                }
                $path .= substr(dirname($sourceUrl).'/', strlen($targetDir) + 1);
            }
        }

        $callback = function($matches) use($host, $path)
        {
            if (false !== strpos($matches['url'], '://')) {
                // absolute
                return $matches[0];
            }

            if ('/' == $matches['url'][0]) {
                // root relative
                return str_replace($matches['url'], $host.$matches['url'], $matches[0]);
            }

            // document relative
            $url = $matches['url'];
            while (0 === strpos($url, '../') && 2 <= substr_count($path, '/')) {
                $path = substr($path, 0, strrpos(rtrim($path, '/'), '/') + 1);
                $url = substr($url, 3);
            }

            return str_replace($matches['url'], $host.$path.$url, $matches[0]);
        };

        $content = $asset->getContent();

        $content = preg_replace_callback('/url\((["\']?)(?<url>.*)(\\1)\)/', $callback, $content);
        $content = preg_replace_callback('/import (["\'])(?<url>.*)(\\1)/', $callback, $content);

        $asset->setContent($content);
    }
}
