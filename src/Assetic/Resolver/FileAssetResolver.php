<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Resolver;

use Assetic\Asset\FileAsset;

/**
 * Transforms filesystem path into asset.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class FileAssetResolver implements AssetResolverInterface
{
    private $root;

    /**
     * Constructor.
     *
     * @param string  $root   The default root directory
     */
    public function __construct($root)
    {
        $this->root = $root;
    }

    /**
     * Parses an input string into an asset.
     *
     * @param string $input   An input string
     * @param array  $options An array of options
     *
     * @return AssetInterface An asset
     */
    public function resolve($input, array $options = array())
    {
        list($root, $path, $input) = $this->prepareRootPathInput($input, $options);

        if (file_exists($input) && is_file($input)) {
            return $this->createFileAsset($input, $root, $path, $options['vars']);
        }
    }

    protected function createFileAsset($source, $root = null, $path = null, $vars)
    {
        return new FileAsset($source, array(), $root, $path, $vars);
    }

    protected function prepareRootPathInput($input, array $options = array())
    {
        if (self::isAbsolutePath($input)) {
            if ($root = self::findRootDir($input, $options['root'])) {
                $path = ltrim(substr($input, strlen($root)), '/');
            } else {
                $path = null;
            }
        } else {
            $root  = $this->root;
            $path  = $input;
            $input = $this->root.'/'.$path;
        }

        return array($root, $path, $input);
    }

    /**
     * Loops through the root directories and returns the first match.
     *
     * @param string $path  An absolute path
     * @param array  $roots An array of root directories
     *
     * @return string|null The matching root directory, if found
     */
    static protected function findRootDir($path, array $roots)
    {
        foreach ($roots as $root) {
            if (0 === strpos($path, $root)) {
                return $root;
            }
        }
    }

    static protected function isAbsolutePath($path)
    {
        return '/' == $path[0] || '\\' == $path[0] || (3 < strlen($path) && ctype_alpha($path[0]) && $path[1] == ':' && ('\\' == $path[2] || '/' == $path[2]));
    }
}
