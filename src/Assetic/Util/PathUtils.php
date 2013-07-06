<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Util;
use Assetic\Asset\AssetInterface;

/**
 * Utils for path manipulation.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
abstract class PathUtils
{
    /**
     * Returns relative path to the source directory from the target path.
     *
     * @return string
     */
    public static function resolveRelative($sourcePath, $targetPath)
    {
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
        }

        return $path;
    }

    /**
     * Resolves an URL from an asset.
     *
     * @param AssetInterface $asset the asset containing the URL
     * @param string         $url   url read in file
     *
     * @return string an URL, a filepath
     */
    public static function resolveUrl(AssetInterface $asset, $url)
    {
        // given URL is absolute URL
        if (false !== strpos($url, '://')) {
            return $url;
        }

        $root = $asset->getSourceRoot();
        $path = dirname($asset->getTargetPath());

        if ('.' === $path) {
            $image = $url;
        } else {
            $image = $path.'/'.$url;
        }

        if (null !== $root) {
            $image = $root.'/'.$image;
        }

        // cleanup local URLs
        if (false === strpos($image, '://')) {
            $image = self::removeQueryString($image);
            $image = self::removeAnchor($image);

            return self::resolveUps($image);
        }

        return $image;
    }

    /**
     * Resolves "../" segments in a path.
     *
     * "foo/bar/../baz" will return "foo/baz".
     *
     * @param string $path
     *
     * @return string
     */
    public static function resolveUps($path)
    {
        $parts = array();
        foreach (explode('/', $path) as $part) {
            if ('..' === $part && count($parts) && '..' !== end($parts)) {
                array_pop($parts);
            } else {
                $parts[] = $part;
            }
        }

        return implode('/', $parts);
    }

    /**
     * Removes from "?" position in string. If not found,
     * returns the original string.
     *
     * @param string $path
     *
     * @return string
     */
    public static function removeQueryString($path)
    {
        if (false === $pos = strpos($path, '?')) {
            return $path;
        }

        $anchorPos = strpos($path, '#', $pos);

        $end = false === $anchorPos ? strlen($path) : $anchorPos;

        return substr($path, 0, $pos).substr($path, $end);
    }

    /**
     * Removes from "#" position in string. If not found,
     * returns the original string.
     *
     * @param string $path
     *
     * @return string
     */
    public static function removeAnchor($path)
    {
        if (false !== $pos = strpos($path, '#')) {
            return substr($path, 0, $pos);
        }

        return $path;
    }

    /**
     * Tests if an URL is a path or not.
     *
     * If not, it's an absolute or protocol-relative or data uri.
     *
     * @param string $url
     *
     * @return boolean
     */
    public static function isPath($url)
    {
        return false === strpos($url, '://') && 0 !== strpos($url, '//') && 0 !== strpos($url, 'data:');
    }

    /**
     * Tests if given path is a root path.
     *
     * @return boolean
     */
    public static function isRootPath($url)
    {
        return isset($url[0]) && '/' == $url[0];
    }

    final private function __construct() { }
}
