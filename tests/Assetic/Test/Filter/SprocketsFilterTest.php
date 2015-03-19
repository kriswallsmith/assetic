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
use Assetic\Filter\SprocketsFilter;

/**
 * @group integration
 */
class SprocketsFilterTest extends FilterTestCase
{
    private $filter;
    private $assetRoot;

    protected function setUp()
    {
        if (!$rubyBin = $this->findExecutable('ruby', 'RUBY_BIN')) {
            $this->markTestSkipped('Unable to locate `ruby` executable.');
        }

        if (!isset($_SERVER['SPROCKETS_LIB'])) {
            $this->markTestSkipped('There is no SPROCKETS_LIB environment variable.');
        }

        $this->filter = new SprocketsFilter($_SERVER['SPROCKETS_LIB'], $rubyBin);

        $this->assetRoot = sys_get_temp_dir().DIRECTORY_SEPARATOR.uniqid('assetic_sprockets');
        if (is_dir($this->assetRoot)) {
            self::removeDirectory($this->assetRoot);
        }

        mkdir($this->assetRoot);
    }

    protected function tearDown()
    {
        self::removeDirectory($this->assetRoot);

        $this->filter = null;
        $this->assetRoot = null;
    }

    public function testFilterLoad()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/sprockets/main.js');
        $asset->load();

        $this->filter->addIncludeDir(__DIR__.'/fixtures/sprockets/lib1');
        $this->filter->addIncludeDir(__DIR__.'/fixtures/sprockets/lib2');
        $this->filter->setAssetRoot($this->assetRoot);
        $this->filter->filterLoad($asset);

        $this->assertContains('/* header.js */', $asset->getContent());
        $this->assertContains('/* include.js */', $asset->getContent());
        $this->assertContains('/* footer.js */', $asset->getContent());
        $this->assertFileExists($this->assetRoot.'/images/image.gif');
    }
}
