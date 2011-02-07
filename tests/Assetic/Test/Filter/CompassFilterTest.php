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
    public function testFilterLoad()
    {
        if (!isset($_SERVER['COMPASS_PATH'])) {
            $this->markTestSkipped('There is no COMPASS_PATH environment variable.');
        }
        
        $asset = new FileAsset(__DIR__.'/fixtures/compass/stylesheet.scss');
        
        $this->_testAsset($asset);
    }
    
    public function testFilterLoadWithLongPath()
    {
        if (!isset($_SERVER['COMPASS_PATH'])) {
            $this->markTestSkipped('There is no COMPASS_PATH environment variable.');
        }
        
        $pathLongerThanSysTempDir = 'very-long-path--long-enough-to-be-longer-than-a-normal-temporary-temp-dir-in-any--filesystem--i-think-it-is-okay--one-more-time-now-that-is-ok';
        
        $asset = new FileAsset(__DIR__.'/fixtures/compass/' . $pathLongerThanSysTempDir . '/stylesheet.scss');
        
        $this->_testAsset($asset);
    }
    
    private function _testAsset($asset)
    {
        $asset->load();

        $filter = new CompassFilter();
        $filter->filterLoad($asset);

        $this->assertContains('.test-class', $asset->getContent());
        $this->assertContains('font-size: 2em;', $asset->getContent());
    }
}
