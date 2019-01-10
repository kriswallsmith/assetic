<?php

/*
   ____  __      __    ___  _  _  ___    __   ____     ___  __  __  ___
  (  _ \(  )    /__\  / __)( )/ )/ __)  /__\ (_  _)   / __)(  \/  )/ __)
   ) _ < )(__  /(__)\( (__  )  (( (__  /(__)\  )(    ( (__  )    ( \__ \
  (____/(____)(__)(__)\___)(_)\_)\___)(__)(__)(__)    \___)(_/\/\_)(___/

   @author          Black Cat Development
   @copyright       Black Cat Development
   @link            https://blackcat-cms.org
   @license         http://www.gnu.org/licenses/gpl.html
   @category        CAT_Core
   @package         CAT_Core

*/

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use \CAT\Helper\Directory as Directory;

class CATSourcemapFilter implements FilterInterface
{
    public function filterLoad(AssetInterface $asset)
    {
        $content = $asset->getContent();
        $file    = $asset->getSourcePath();
        $path    = dirname($file);

        /*# sourceMappingURL=bootstrap.min.css.map */
        preg_match_all('~# sourcemappingurl=([\w\.]+)\s?~i', $content, $matches, PREG_PATTERN_ORDER);
        if(count($matches)>0) {
            for($i=0;$i<count($matches);$i++) {
                if(isset($matches[1][$i])) {
                    \CAT\Helper\Assets::$sourcemaps[] = Directory::sanitizePath($path.'/'.$matches[1][$i]);
                }
            }
        }
    }

    public function filterDump(AssetInterface $asset)
    {

    }
}
