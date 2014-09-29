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

/**
 * Loads resources from multiple directories.
 *
 * Use this loader if you want to override resources from one directory in
 * another directory.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @see load()
 */
class CoalescingDirectoryLoader
{
    /**
     * Loads resources from a list of directories.
     *
     * If two files in two different directories have the same relative path
     * to their directory, the file of the latter directory overrides the one
     * in the former. For example, take that the following files exist:
     *
     *     - /acme/blog/views/layout.html.twig
     *     - /app/views/layout.html.twig
     *
     * If you load the following directories:
     *
     *     $loader = new CoalescingDirectoryLoader();
     *     $resources = $loader->load(array(
     *         '/acme/blog',
     *         '/app'.
     *     ));
     *
     * Then only the layout.html.twig file of the "/app" directory is
     * returned by the loader.
     *
     * You can optionally filter the returned resources by passing a regular
     * expression for the file name in the $pattern argument.
     *
     * @param array       $directories A list of directory paths
     * @param string|null $pattern     A regular expression or null if you
     *                                 don't want to filter resources
     *
     * @return \Assetic\Factory\Resource\FileResource[] An array of file resources
     *
     * @throws \InvalidArgumentException If any of the directories does not exist
     */
    public function load($directories, $pattern = null)
    {
        $directoryLoader = new DirectoryLoader();
        $resources = array();

        foreach ($directories as $directory) {
            $resources = array_replace(
                $directoryLoader->loadByRelativePath($directory, $pattern),
                // Directories listed earlier in the array take precedence
                $resources
            );
        }

        return array_values($resources);
    }
}
