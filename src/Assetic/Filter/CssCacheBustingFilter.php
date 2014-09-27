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
    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $versionFormat;

    /**
     * @param string $version the version string
     * @param string $versionFormat sprintf compatible format string
     */
    public function __construct($version = '', $versionFormat = '%s?%s')
    {
        $this->versionFormat = $versionFormat;
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        $version = $this->version;
        $versionFormat = $this->versionFormat;

        $content = $this->filterReferences(
            $asset->getContent(),
            function ($matches) use ($version, $versionFormat) {
                return str_replace(
                    $matches['url'],
                    sprintf($versionFormat, $matches['url'], $version),
                    $matches[0]
                );
            }
        );

        $asset->setContent($content);
    }
}
