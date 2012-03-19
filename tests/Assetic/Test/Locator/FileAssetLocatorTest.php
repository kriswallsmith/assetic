<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Locator;

use Assetic\Locator\FileAssetLocator;

class FileAssetLocatorTest extends \PHPUnit_Framework_TestCase
{
    private $locator;

    protected function setUp()
    {
        $this->locator = new FileAssetLocator(__DIR__.'/../Fixture/root');
    }

    public function filesystemPaths()
    {
        return array(
            array('foo.js'),
            array('bar.js'),
            array('css/more.sass'),
        );
    }

    /**
     * @dataProvider filesystemPaths
     */
    public function testCorrectInput($input)
    {
        $asset = $this->locator->locate($input, array('vars' => array()));

        $this->assertNotNull($asset);
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $asset);
    }

    public function testWrongInput()
    {
        $asset = $this->locator->locate('css/jquery.js', array('vars' => array()));

        $this->assertNull($asset);
    }
}
