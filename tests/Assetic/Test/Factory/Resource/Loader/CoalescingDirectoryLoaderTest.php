<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Factory\Resource\Loader;

use Assetic\Factory\Resource\Loader\CoalescingDirectoryLoader;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class CoalescingDirectoryLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CoalescingDirectoryLoader
     */
    private $loader;

    protected function setUp()
    {
        $this->loader = new CoalescingDirectoryLoader();
    }

    public function testLoad()
    {
        // notice only one directory has a trailing slash
        $resources = $this->loader->load(array(
            __DIR__.'/../Fixtures/dir1/',
            __DIR__.'/../Fixtures/dir2',
        ), '/\.txt$/');

        $paths = array();
        foreach ($resources as $resource) {
            $paths[] = realpath((string) $resource);
        }
        sort($paths);

        $this->assertEquals(array(
            realpath(__DIR__.'/../Fixtures/dir1/file1.txt'),
            realpath(__DIR__.'/../Fixtures/dir1/file2.txt'),
            realpath(__DIR__.'/../Fixtures/dir2/file3.txt'),
        ), $paths, 'files from multiple directories are merged');
    }

}
