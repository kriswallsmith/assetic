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
use Assetic\Exception\FilterException;
use Assetic\Factory\AssetFactory;

use Stylus\Stylus;

/**
 * Loads STYL files.
 *
 * @link https://github.com/AustP/Stylus.php
 * @author Linus Unnebäck <linus@folkdatorn.se>
 */
class StylusphpFilter implements DependencyExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        $stylus = new Stylus();

        if ($asset->getSourceRoot()) {

            $stylus->setReadDir($asset->getSourceRoot());
            $stylus->setImportDir($asset->getSourceDirectory());

            $stylus->formFile($asset->getSourcePath());

        } else {

            $stylus->fromString($asset->getContent());

        }

        $asset->setContent($stylus->toString());
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
    }

    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        // todo
        return array();
    }
}
