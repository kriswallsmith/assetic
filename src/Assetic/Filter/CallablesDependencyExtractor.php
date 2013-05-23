<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Factory\AssetFactory;

/**
 * Like CallablesFilter, but it is also a DependencyExtractor
 *
 * @author Matthias Pigulla <mp@webfactory.de>
 */
class CallablesDependencyExtractor extends CallablesFilter implements DependencyExtractorInterface
{

    protected $extractor;

    public function __construct($loader = null, $dumper = null, $extractor = null)
    {
        parent::__construct($loader, $dumper);
        $this->extractor = $extractor;
    }

    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        if (null !== $callable = $this->extractor) {
            return $callable($factory, $content, $loadPath);
        }
    }

}
