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
 *
 * Less files are mostly compatible, but there are slight differences.
 *
 * To use this, you need to clone https://github.com/leafo/lessphp and make
 * sure to either include lessphp.inc.php or tell your autoloader that's where
 * lessc is located.
 *
 * @link http://leafo.net/lessphp/
 * @author David Buchmann <david@liip.ch>
 */
class LessphpFilter implements FilterInterface
{
    private $baseDir;

    /**
     * Constructor.
     *
     * @param string $baseDir The base web directory
     */
    public function __construct($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $sourceUrl = $asset->getSourceUrl();
        if ($sourceUrl && false === strpos($sourceUrl, '://')) {
            $baseDir = self::isAbsolutePath($sourceUrl) ? '' : $this->baseDir.'/';
            $sourceUrl = $baseDir.$sourceUrl;
        }

        $lc = new \lessc($sourceUrl);

        // the way lessc::parse is implemented, the content wins if both url and content are defined
        $asset->setContent($lc->parse($asset->getContent()));
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    static private function isAbsolutePath($path)
    {
        return '/' == $path[0] || '\\' == $path[0] || (3 < strlen($path) && ctype_alpha($path[0]) && $path[1] == ':' && ('\\' == $path[2] || '/' == $path[2]));
    }
}
