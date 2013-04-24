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

use Assetic\Asset\AssetInterface;

use MattCG\cjsDelivery\DeliveryFactory;

/**
 * Loads JavaScript files using cjsDelivery.
 *
 * @link https://github.com/mattcg/cjsDelivery
 *
 * @author Matthew Caruana Galizia <m@m.cg>
 */
class CjsDeliveryFilter implements FilterInterface
{

    private $minifyIdentifiers = false;
    private $includes;
    private $parsePragmas = false;
    private $pragmaFormat;
    private $pragmas = array();

    public function setMinifyIdentifiers($minifyIdentifiers)
    {
        $this->minifyIdentifiers = $minifyIdentifiers;
    }

    public function setIncludes($includes)
    {
        $this->includes = $includes;
    }

    public function setPragmaFormat($pragmaFormat)
    {
        $this->pragmaFormat = $pragmaFormat;
    }

    public function setParsePragmas($parsePragmas)
    {
        $this->parsePragmas = $parsePragmas;
    }

    public function setPragmas($pragmas)
    {
        $this->pragmas = $pragmas;
    }

    public function filterLoad(AssetInterface $asset) {
        $filepath = $asset->getSourceRoot() . '/' . $asset->getSourcePath();
        $moduleidentifier = $this->stripExtension($filepath);

        $options = array();
        $options['includes'] = $this->includes;
        $options['minifyIdentifiers'] = $this->minifyIdentifiers;
        $options['parsePragmas'] = $this->parsePragmas;
        $options['pragmas'] = $this->pragmas;
        $options['pragmaFormat'] = $this->pragmaFormat;

        $delivery = DeliveryFactory::create($options);
        $delivery->addModule($moduleidentifier, $asset->getContent());
        $delivery->setMainModule($moduleidentifier);

        $content = $delivery->getOutput();
        $asset->setContent($content);
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    private function stripExtension($filepath)
    {
        return preg_replace('/\.js$/', '', $filepath);
    }
}
