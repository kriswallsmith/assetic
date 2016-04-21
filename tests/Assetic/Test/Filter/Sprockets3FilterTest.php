<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\Sprockets3Filter;
use Assetic\Util\FilesystemUtils;

/**
 * @group integration
 */
class Sprockets3FilterTest extends FilterTestCase
{
    /** @var  Sprockets3Filter */
    private $filter;


    protected function setUp()
    {
        if (!$rubyBin = $this->findExecutable('ruby', 'RUBY_BIN')) {
            $this->markTestSkipped('Unable to locate `ruby` executable.');
        }

        if (!isset($_SERVER['SPROCKETS3_LIB'])) {
            $this->markTestSkipped('There is no SPROCKETS3_LIB environment variable.');
        }

        $this->filter = new Sprockets3Filter($_SERVER['SPROCKETS3_LIB'], $rubyBin);
    }

    /**
     * @expectedException \Assetic\Exception\FilterException
     * @expectedExceptionMessageRegExp ~couldn't find file 'libxyz\/main'~
     */
    public function testFilerThrows()
    {
        $asset = new FileAsset(__DIR__ . '/fixtures/sprockets3/brokenDeps.js');
        $asset->load();

        $this->filter->addIncludeDir(__DIR__ . '/fixtures/sprockets3');
        $this->filter->filterLoad($asset);
    }

    public function testFilterLoad()
    {
        $asset = new FileAsset(__DIR__ . '/fixtures/sprockets3/require.js');
        $asset->load();

        $this->filter->addIncludeDir(__DIR__ . '/fixtures/sprockets3');
        $this->filter->addIncludeDir(__DIR__ . '/fixtures/sprockets3/lib/bar');
        $this->filter->filterLoad($asset);

        $this->assertContains('/* lib/foo/foo.js */', $asset->getContent());
        $this->assertContains('/* lib/bar/bar.js */', $asset->getContent());
        $this->assertContains('/* lib/main.js */', $asset->getContent());
        $this->assertContains('/* require.js */', $asset->getContent());
    }

    public function testRequireTree()
    {
        $asset = new FileAsset(__DIR__ . '/fixtures/sprockets3/requireTree.js');
        $asset->load();

        $this->filter->addIncludeDir(__DIR__ . '/fixtures/sprockets3');
        $this->filter->filterLoad($asset);

        $this->assertContains('/* lib/foo/foo.js */', $asset->getContent());
        $this->assertContains('/* lib/bar/bar.js */', $asset->getContent());
        $this->assertContains('/* lib/main.js */', $asset->getContent());
        $this->assertContains('/* requireTree.js */', $asset->getContent());
    }

    public function testCircularDeps()
    {
        $asset = new FileAsset(__DIR__ . '/fixtures/sprockets3/circular.js');
        $asset->load();

        $this->filter->addIncludeDir(__DIR__ . '/fixtures/sprockets3');
        $this->filter->filterLoad($asset);

        $this->assertContains('/* circularBack.js */', $asset->getContent());
        $this->assertContains('/* circular.js */', $asset->getContent());
    }

    public function testCss()
    {
        $asset = new FileAsset(__DIR__ . '/fixtures/sprockets3/main.css');
        $asset->load();

        $this->filter->addIncludeDir(__DIR__ . '/fixtures/sprockets3');
        $this->filter->filterLoad($asset);

        $this->assertContains('/* bootstrap.css */', $asset->getContent());
        $this->assertContains('/* main.css */', $asset->getContent());
    }
}
