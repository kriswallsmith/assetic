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
    const EXT_JS = 'js';

    private $minifyidentifiers = false;
    private $includes = null;
    private $parsepragmas = false;
    private $pragmaformat = null;
    private $pragmas = null;

    private function stripExtension($filepath)
    {
        return preg_replace('/\.' . self::EXT_JS . '$/', '', $filepath);
    }

    public function setMinifyIdentifiers($minifyidentifiers)
    {
        $this->minifyidentifiers = $minifyidentifiers;
    }

    public function setIncludes($includes)
    {
        $this->includes = $includes;
    }

    public function setPragmaFormat($pragmaformat)
    {
        $this->pragmaformat = $pragmaformat;
    }

    public function setParsePragmas($parsepragmas)
    {
        $this->parsepragmas = $parsepragmas;
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
        $options['minifyIdentifiers'] = $this->minifyidentifiers;
        $options['parsePragmas'] = $this->parsepragmas;
        $options['pragmas'] = $this->pragmas;
        $options['pragmaFormat'] = $this->pragmaformat;

        $delivery = DeliveryFactory::create($options);
        $delivery->addModule($moduleidentifier, $asset->getContent());
        $delivery->setMainModule($moduleidentifier);

        $content = $delivery->getOutput();
        $asset->setContent($content);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
