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

/**
 * Fixes relative CSS urls.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Bianka Martinovic <info@webbird.de>
 */
class CATCssRewriteFilter extends BaseCssFilter
{
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $sourceBase = $asset->getSourceRoot();
        $sourcePath = $asset->getSourcePath();
        $targetPath = $asset->getTargetPath();

        if (null === $sourcePath || null === $targetPath || $sourcePath == $targetPath) {
            return;
        }

        // learn how to get from the target back to the source
        if (false !== strpos($sourceBase, '://')) {
            list($scheme, $url) = explode('://', $sourceBase.'/'.$sourcePath, 2);
            list($host, $path) = explode('/', $url, 2);

            $host = $scheme.'://'.$host.'/';
            $path = false === strpos($path, '/') ? '' : dirname($path);
            $path .= '/';
        } else {
            // assume source and target are on the same host
            $host = '';

            // pop entries off the target until it fits in the source
            if ('.' == dirname($sourcePath)) {
                $path = str_repeat('../', substr_count($targetPath, '/'));
            } elseif ('.' == $targetDir = dirname($targetPath)) {
                $path = dirname($sourcePath).'/';
            } else {
                $path = '';
                while (0 !== strpos($sourcePath, $targetDir)) {
                    if (false !== $pos = strrpos($targetDir, '/')) {
                        $targetDir = substr($targetDir, 0, $pos);
                        $path .= '../';
                    } else {
                        $targetDir = '';
                        $path .= '../';
                        break;
                    }
                }
                $path .= ltrim(substr(dirname($sourcePath).'/', strlen($targetDir)), '/');
                $path = str_replace('../', '', $path);

            }
        }

        $content = $this->filterReferences($asset->getContent(), function ($matches) use ($host, $path) {
            // absolute or protocol-relative or data uri
            if (false !== strpos($matches['url'], '://') || 0 === strpos($matches['url'], '//') || 0 === strpos($matches['url'], 'data:')) {
                return $matches[0];
            }

            // root relative
            if (isset($matches['url'][0]) && '/' == $matches['url'][0]) {
                return str_replace($matches['url'], $host.$matches['url'], $matches[0]);
            }

            // document relative
            $url = $matches['url'];
//            while (0 === strpos($url, '../') && 2 <= substr_count($path, '/')) {

                $pathinfo = pathinfo($url);
                $filename = $pathinfo['basename'];

                if(substr_count($pathinfo['basename'],'?'))
                    list($filename,$ignore) = explode('?',$pathinfo['basename'],2);
                $fullpath = Directory::sanitizePath(CAT_ENGINE_PATH.'/'.$path.'/'.$pathinfo['dirname'].'/'.$filename);

                if(file_exists($fullpath))
                {
                    if(!file_exists(CAT_PATH.'/assets') || !is_dir(CAT_PATH.'/assets'))
                        Directory::createDirectory(CAT_PATH.'/assets');
                    if(!file_exists(CAT_PATH.'/assets/'.$filename))
                        copy($fullpath,CAT_PATH.'/assets/'.$filename);
                    $url = CAT_SITE_URL.'/assets/'.$pathinfo['basename'];
                    $path = '';
                }
                else
                {
                    $path = substr($path, 0, strrpos(rtrim($path, '/'), '/') + 1);
                    $url = substr($url, 3);
                }

//            }

            $parts = array();
            foreach (explode('/', $host.$path.$url) as $part) {
                if ('..' === $part && count($parts) && '..' !== end($parts)) {
                    array_pop($parts);
                } else {
                    $parts[] = $part;
                }
            }

            return str_replace($matches['url'], implode('/', $parts), $matches[0]);
        });

        $asset->setContent($content);
    }
}
