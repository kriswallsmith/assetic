<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\SprocketsFilter;

class SprocketsFilterTest extends \PHPUnit_Framework_TestCase
{
    private $assetRoot;

    protected function setUp()
    {
        $this->assetRoot = sys_get_temp_dir().'/assetic_sprockets';
        if (is_dir($this->assetRoot)) {
            shell_exec('rm -rf '.escapeshellarg($this->assetRoot).'/*');
        } else {
            mkdir($this->assetRoot);
        }
    }

    protected function tearDown()
    {
        shell_exec('rm -rf '.escapeshellarg($this->assetRoot).'/*');
    }

    /**
     * @group integration
     */
    public function testFilterLoad()
    {
        if (!isset($_SERVER['SPROCKETIZE_BIN'])) {
            $this->markTestSkipped('There is no SPROCKETIZE_BIN environment variable.');
        }

        $asset = new FileAsset(__DIR__.'/fixtures/sprockets/main.js', array(), 'main.js');
        $asset->load();

        $filter = new SprocketsFilter(__DIR__.'/fixtures/sprockets', $_SERVER['SPROCKETIZE_BIN']);
        $filter->addIncludeDir(__DIR__.'/fixtures/sprockets/lib1');
        $filter->addIncludeDir(__DIR__.'/fixtures/sprockets/lib2');
        $filter->setAssetRoot($this->assetRoot);
        $filter->filterLoad($asset);

        $this->assertContains('/* header.js */', $asset->getContent());
        $this->assertContains('/* include.js */', $asset->getContent());
        $this->assertContains('/* footer.js */', $asset->getContent());
        $this->assertFileExists($this->assetRoot.'/images/image.gif');
    }
}
