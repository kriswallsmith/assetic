<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test;

use Assetic\AbstractExtension;

class AbstractExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;

    protected function setUp()
    {
        $this->extension = new AbstractExtensionStub();
    }

    protected function tearDown()
    {
        unset($this->extension);
    }

    /**
     * @test
     * @dataProvider provideVisitorMethods
     */
    public function shouldReturnEmptyArrays($method)
    {
        $this->assertInternalType('array', $this->extension->$method());
    }

    public function provideVisitorMethods()
    {
        return array(
            array('getLoaderVisitors'),
            array('getProcessorVisitors'),
        );
    }
}

class AbstractExtensionStub extends AbstractExtension
{
}
