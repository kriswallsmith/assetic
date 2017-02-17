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
use Assetic\Filter\SmartSpritesFilter;

/**
 * @group integration
 */
class SmartSpritesFilterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!isset($_SERVER['SMART_SPRITES_DIR'])) {
            $this->markTestSkipped('There is no SMART_SPRITES_DIR');
        }
    }

    public function testNonSpritedFile()
    {
        $asset = new FileAsset(__DIR__ . '/fixtures/smartsprites/notsprited_test.css');
        $asset->load();

        $filter = new SmartSpritesFilter($_SERVER['SMART_SPRITES_DIR']);
        $filter->filterLoad($asset);

        $this->assertContains('/home.gif', $asset->getContent());
        $this->assertContains('/home.png', $asset->getContent());
    }

    public function testSpritedFile()
    {
        $asset = new FileAsset(__DIR__ . '/fixtures/smartsprites/sprited_test.css');
        $asset->load();

        $filter = new SmartSpritesFilter($_SERVER['SMART_SPRITES_DIR']);
        $filter->setDocumentRoot(__DIR__.'/fixtures/');
        $filter->filterLoad($asset);

        $this->assertContains('../mysprite.png', $asset->getContent());
        $this->assertContains('/mysprite.png', $asset->getContent());
        
        unlink(__DIR__ . '/fixtures/smartsprites/sprited_test-sprite.css');
        unlink(__DIR__ . '/fixtures/mysprite.png');
    }
}
