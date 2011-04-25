<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\CompassFilter;

/**
 * Compass Filter test case
 * 
 * @author Maxime Thirouin <dev@moox.fr>
 */
class CompassFilterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!isset($_SERVER['COMPASS_BIN']) or !isset($_SERVER['SASS_BIN'])) {
            $this->markTestSkipped('There is no COMPASS_BIN or SASS_BIN environment variable.');
        }
    }

    public function testFilterLoadWithScss()
    {
        $this->_testAsset(__DIR__ . '/fixtures/compass/stylesheet.scss');
    }

    public function testFilterLoadWithSass()
    {
        $this->_testAsset(__DIR__ . '/fixtures/compass/stylesheet.sass');
    }
    
    private function _testAsset($filePath)
    {
        $asset = new FileAsset($filePath);
        $asset->load();

        $filter = new CompassFilter();
        $filter->addLoadPath(dirname($filePath));

        // there is just a trick for selecting sass or scss (sass does not select the right syntax automatically)
        if (preg_match('#\.scss$#', $filePath))
        {
            $filter->setScss(true);
        }

        $filter->filterLoad($asset);

        $this->assertContains('.test-class', $asset->getContent());
        $this->assertContains('font-size: 2em;', $asset->getContent());
    }
}
