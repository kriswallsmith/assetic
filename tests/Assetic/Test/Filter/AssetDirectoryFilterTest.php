<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\AssetDirectoryFilter;

class AssetDirectoryFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $directory = $this->getMockBuilder('Assetic\Util\AssetDirectory')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $directory
            ->expects($this->any())
            ->method('getTarget')
            ->will($this->returnValue('assets'))
        ;

        $directory
            ->expects($this->once())
            ->method('add')
            ->with('images/foo.png')
            ->will($this->returnValue('assets/foo.png'))
        ;

        $filter = new AssetDirectoryFilter($directory);

        $asset = new StringAsset('body { background: url("../images/foo.png"); }', array($filter), null, 'css/main.css');
        $asset->setTargetPath('css/main.css');
        $asset->load();

        $filter->filterLoad($asset);
        $filter->filterDump($asset);

        $this->assertEquals('body { background: url("../assets/foo.png"); }', $asset->getContent(), 'AssetDirectoryFilter filters URL');
    }
}
