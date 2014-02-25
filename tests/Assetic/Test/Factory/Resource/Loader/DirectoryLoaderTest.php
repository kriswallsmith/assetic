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

use Assetic\Factory\Resource\Loader\DirectoryLoader;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class DirectoryLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DirectoryLoader
     */
    private $loader;

    protected function setUp()
    {
        $this->loader = new DirectoryLoader();
    }

    /**
     * @dataProvider getPatternsAndEmpty
     */
    public function testLoad($pattern, $empty)
    {
        $resources = $this->loader->load(__DIR__.'/..', $pattern);

        foreach ($resources as $resource) {
            $this->assertInstanceOf('Assetic\\Factory\\Resource\\ResourceInterface', $resource);
        }

        if ($empty) {
            $this->assertCount(0, $resources);
        } else {
            $this->assertGreaterThan(0, count($resources));
        }
    }

    public function testLoadByRelativePath()
    {
        $resources = $this->loader->loadByRelativePath(__DIR__.'/../Fixtures');

        $values = $this->loader->load(__DIR__.'/../Fixtures');

        $this->assertEquals($values, array_values($resources));

        $keys = array(
            'css/style.css',
            'dir1/file1.txt',
            'dir1/file2.txt',
            'dir2/file1.txt',
            'dir2/file3.txt',
        );

        // Order is not determined
        ksort($resources);

        $this->assertEquals($keys, array_keys($resources));
    }

    public function getPatternsAndEmpty()
    {
        return array(
            array(null, false),
            array('/\.php$/', false),
            array('/\.foo$/', true),
        );
    }

    public function testLoadRecursively()
    {
        $resources = $this->loader->load(realpath(__DIR__.'/..'), '/^'.preg_quote(basename(__FILE__)).'$/');

        $this->assertCount(1, $resources);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDirectory()
    {
        $this->loader->load(__DIR__.'foo');
    }

    public function testFollowSymlinks()
    {
        // Create the symlink if it doesn't already exist yet (if someone broke the entire testsuite perhaps)
        if (!is_dir(__DIR__.'/../Fixtures/dir3')) {
            symlink(__DIR__.'/../Fixtures/dir2', __DIR__.'/../Fixtures/dir3');
        }

        $resources = $this->loader->load(__DIR__.'/../Fixtures');

        $this->assertCount(7, $resources);
    }

    protected function tearDown()
    {
        if (is_dir(__DIR__.'/../Fixtures/dir3') && is_link(__DIR__.'/../Fixtures/dir3')) {
            if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
                rmdir(__DIR__.'/../Fixtures/dir3');
            } else {
                unlink(__DIR__.'/../Fixtures/dir3');
            }
        }
    }
}
