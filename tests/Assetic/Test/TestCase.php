<?php

namespace Assetic\Test;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    protected static function removeDirectory($dir)
    {
        $inner = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
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
            if ($file->isDir()) {
                rmdir($file);
            }
        }

        // finally the directory itself
        rmdir($dir);
    }
}
