<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Stylecow\Parser;

/**
 * Filters assets through Stylecow.
 * @link https://github.com/oscarotero/stylecow
 * 
 * @author Luke Mills <luke@lukemills.net>
 */
class StylecowFilter implements FilterInterface {

    /**
     *
     * @var string[]
     */
    private $filters = array();

    /**
     * Clears and sets filters to the array passed in.
     * 
     * @param string[] $filters  An array of Stylecow plugins (@see https://github.com/oscarotero/stylecow#plugins).
     */
    public function setFilters(array $filters) {
        $this->filters = $filters;
    }

    /**
     * Adds a Stylecow plugin to the list of plugins to apply.
     * 
     * @param string $name  A Stylecow plugins (@see https://github.com/oscarotero/stylecow#plugins).
     */
    public function setFilter($name) {
        if (!in_array($name, $this->filters)) {
            $this->filters[] = $name;
        }
    }

    public function filterDump(AssetInterface $asset) {
        
    }

    public function filterLoad(AssetInterface $asset) {
        $css = Parser::parseString($asset->getContent());
        $css->applyPlugins($this->filters);
        $asset->setContent($css->__toString());
    }

}