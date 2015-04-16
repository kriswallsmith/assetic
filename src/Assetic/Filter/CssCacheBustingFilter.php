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

/**
 * Class CssCacheBustingFilter
 *
 * @package Assetic\Filter
 * @author Maximilian Reichel <info@phramz.com>
 */
class CssCacheBustingFilter extends BaseCssFilter
{
    private $version;
    private $format = '%s?%s';

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function setFormat($versionFormat)
    {
        $this->format = $versionFormat;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        if (!$this->version) {
            return;
        }

        $version = $this->version;
        $format = $this->format;

        $asset->setContent($this->filterReferences(
            $asset->getContent(),
            function ($matches) use ($version, $format) {
                if (0 === strpos($matches['url'], 'data:')) {
                    return $matches[0];
                }

                $query = parse_url($matches['url'], PHP_URL_QUERY);
                $fragment = parse_url($matches['url'], PHP_URL_FRAGMENT);

                // Remove fragment and query parameters from URL
                $url = preg_replace('/(?:#|\?).*$/', '', $matches['url']);

                $suffix = $version;
                if ($query !== null) {
                    $suffix .= "&{$query}";
                }

                if ($fragment !== null) {
                    $suffix .= "#{$fragment}";
                }

                return str_replace(
                    $matches['url'],
                    sprintf($format, $url, $suffix),
                    $matches[0]
                );
            }
        ));
    }
}
