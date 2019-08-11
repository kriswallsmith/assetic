<?php namespace Assetic\Util;

/**
 * Filesystem utilities.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FilesystemUtils
{
    /**
     * Recursively removes a directory from the filesystem.
     */
    public static function removeDirectory($directory)
    {
        $inner = new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS);
        $outer = new \RecursiveIteratorIterator($inner, \RecursiveIteratorIterator::SELF_FIRST);

        // remove the files first
        foreach ($outer as $file) {
            if ($file->isFile()) {
                unlink($file);
            }
        }

        // remove the sub-directories next
        $files = iterator_to_array($outer);
        foreach (array_reverse($files) as $file) {
            /** @var \SplFileInfo $file */
            if ($file->isDir()) {
                rmdir($file);
            }
        }

        // finally the directory itself
        rmdir($directory);
    }

    /**
     * Creates a throw-away directory.
     *
     * This is not considered a "temporary" directory because it will not be
     * automatically deleted at the end of the request or process. It must be
     * deleted manually.
     *
     * @param string $prefix A prefix for the directory name
     *
     * @return string The directory path
     */
    public static function createThrowAwayDirectory($prefix)
    {
        $directory = static::getTemporaryDirectory() . DIRECTORY_SEPARATOR . uniqid('assetic_' . $prefix);
        mkdir($directory);

        return $directory;
    }

    /**
     * Creates a temporary file and optionally writes to it.
     *
     * @param string $prefix A prefix for the file name
     * @param string|null $contents Contents to be written to the file, optional
     *
     * @return string The file path
     */
    public static function createTemporaryFile($prefix, $contents = null)
    {
        $tmpFile = tempnam(static::getTemporaryDirectory(), 'assetic_' . $prefix);

        if (!is_null($contents)) {
            file_put_contents($tmpFile, $contents);
        }

        return $tmpFile;
    }

    /**
     * Gets the path to the temporary directory
     *
     * @return string
     */
    public static function getTemporaryDirectory()
    {
        return realpath(sys_get_temp_dir());
    }
}
