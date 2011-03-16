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
 * Loads LESS files using the PHP implementation of less, lessphp.
 * See http://leafo.net/lessphp/
 * Less files are mostly compatible, but there are slight differences.
 *
 * To use this, you need to clone https://github.com/leafo/lessphp
 *
 * @author David Buchmann <david@liip.ch>
 */
class LessphpFilter implements FilterInterface
{
    private $baseDir;

    /**
     * Constructor.
     *
     * @param string $baseDir   The base web directory
     * @param string $lessc_inc The path to the lessc.inc.php file containing the less compiler (no autoload as its not namespaced)
     * @param array  $nodePaths An array of node paths
     */
    public function __construct($baseDir, $lessc_inc)
    {
        require_once($lessc_inc);
        $this->baseDir = $baseDir;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $sourceUrl = $asset->getSourceUrl();

        // parser options
        $parserOptions = array();
        if ($sourceUrl && false === strpos($sourceUrl, '://')) {
            $baseDir = self::isAbsolutePath($sourceUrl) ? '' : $this->baseDir.'/';

            $sourceUrl = $baseDir.$sourceUrl;
        }

        $lc = new \lessc($sourceUrl);

        $asset->setContent($lc->parse());
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    static private function isAbsolutePath($path)
    {
        return '/' == $path[0] || '\\' == $path[0] || (3 < strlen($path) && ctype_alpha($path[0]) && $path[1] == ':' && ('\\' == $path[2] || '/' == $path[2]));
    }
}
