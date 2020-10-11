<?php namespace Assetic\Test\Factory\Resource;

use PHPUnit\Framework\TestCase;
use Assetic\Factory\Resource\CoalescingDirectoryResource;
use Assetic\Factory\Resource\DirectoryResource;

class CoalescingDirectoryResourceTest extends TestCase
{
    /**
     * @test
     */
    public function shouldFilterFiles()
    {
        // notice only one directory has a trailing slash
        $resource = new CoalescingDirectoryResource(array(
            new DirectoryResource(__DIR__.'/Fixtures/dir1/', '/\.txt$/'),
            new DirectoryResource(__DIR__.'/Fixtures/dir2', '/\.txt$/'),
        ));

        $paths = [];
        foreach ($resource as $file) {
            $paths[] = realpath((string) $file);
        }
        sort($paths);

        $this->assertEquals(array(
            realpath(__DIR__.'/Fixtures/dir1/file1.txt'),
            realpath(__DIR__.'/Fixtures/dir1/file2.txt'),
            realpath(__DIR__.'/Fixtures/dir2/file3.txt'),
        ), $paths, 'files from multiple directories are merged');
    }
}
