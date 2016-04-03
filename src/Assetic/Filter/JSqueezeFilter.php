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
 * JSqueeze filter.
 *
 * @link https://github.com/nicolas-grekas/JSqueeze
 * @author Nicolas Grekas <p@tchwork.com>
 */
class JSqueezeFilter implements FilterInterface
{
    private $singleLine = true;
    private $keepImportantComments = true;
    private $className;
    private $specialVarRx = false;
    private $defaultRx;

    public function __construct()
    {
        // JSqueeze is namespaced since 2.x, this works with both 1.x and 2.x
        if (class_exists('\\Patchwork\\JSqueeze')) {
            $this->className = '\\Patchwork\\JSqueeze';
            $this->defaultRx = \Patchwork\JSqueeze::SPECIAL_VAR_PACKER;
        } else {
            $this->className = '\\JSqueeze';
            $this->defaultRx = \JSqueeze::SPECIAL_VAR_RX;
        }
    }

    public function setSingleLine($bool)
    {
        $this->singleLine = (bool) $bool;
    }

    // call setSpecialVarRx(true) to enable global var/method/property
    // renaming with the default regex (for 1.x or 2.x)
    public function setSpecialVarRx($specialVarRx)
    {
        if (true === $specialVarRx) {
            $this->specialVarRx = $this->defaultRx;
        } else {
            $this->specialVarRx = $specialVarRx;
        }
    }

    public function keepImportantComments($bool)
    {
        $this->keepImportantComments = (bool) $bool;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $parser = new $this->className();
        $asset->setContent($parser->squeeze(
            $asset->getContent(),
            $this->singleLine,
            $this->keepImportantComments,
            $this->specialVarRx
        ));
    }
}
