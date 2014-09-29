<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Resource\Loader;

use Assetic\Factory\Resource\FileResource;
use Assetic\Factory\Resource\Util\DirectoryFilterIterator;

/**
 * Loads resources from a directory.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @see load()
 */
class DirectoryLoader
{
    /**
     * Loads resources from a directory.
     *
     * You can optionally filter the returned resources by passing a regular
     * expression for the file name in the $pattern argument.
     *
     * @param string      $directory A directory path
     * @param string|null $pattern   A regular expression or null if you
     *                               don't want to filter resources
     *
     * @return FileResource[] An array of file resources
     *
     * @throws \InvalidArgumentException If the directory does not exist
     */
    public function load($directory, $pattern = null)
    {
        return array_values($this->loadByRelativePath($directory, $pattern));
    }

    /**
     * Loads resources and returns them indexed by their relative path.
     *
     * You can optionally filter the returned resources by passing a regular
     * expression for the file name in the $pattern argument.
     *
     * @param string      $directory A directory path
     * @param string|null $pattern   A regular expression or null if you
     *                               don't want to filter resources
     *
     * @return FileResource[] An array of file resources indexed by their
     *                        relative path to the directory
     *
     * @throws \InvalidArgumentException If the directory does not exist
     */
    public function loadByRelativePath($directory, $pattern = null)
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException(sprintf(
                'The directory "%s" does not exist.',
                $directory
            ));
        }

        // Append directory separator to get the right relative paths
        if (DIRECTORY_SEPARATOR != substr($directory, -1)) {
            $directory .= DIRECTORY_SEPARATOR;
        }

        $resources = array();

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $directory,
                \RecursiveDirectoryIterator::FOLLOW_SYMLINKS
                    | \RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        if (null !== $pattern) {
            $iterator = new DirectoryFilterIterator($iterator, (string) $pattern);
        }

        foreach ($iterator as $file) {
            $file = (string) $file;
            $relativeName = $this->getRelativePath($file, $directory);

            if (!isset($resources[$relativeName])) {
                $resources[$relativeName] = new FileResource($file);
            }
        }

        return $resources;
    }

    /**
     * Returns the relative version of a filename.
     *
     * @param string $file      The file path
     * @param string $directory The directory path
     *
     * @return string The relative path from the directory to the file
     */
    protected function getRelativePath($file, $directory)
    {
        return substr($file, strlen($directory));
    }
}
