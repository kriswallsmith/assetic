<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Asset;

use Assetic\Asset\AbstractAssetVisitor;

class AbstractAssetVisitorTest extends \PHPUnit_Framework_TestCase
{
    private $visitor;

    protected function setUp()
    {
        $this->visitor = new AbstractAssetVisitorStub();
    }

    protected function tearDown()
    {
        unset($this->visitor);
    }

    /**
     * @test
     */
    public function shouldSetPriorityInConstructor()
    {
        $visitor = new AbstractAssetVisitorStub(1);
        $this->assertEquals(1, $visitor->getPriority());
    }

    /**
     * @test
     * @dataProvider provideMethods
     * @expectedException InvalidArgumentException
     */
    public function shouldErrorOnInvalidAsset($method)
    {
        $node = $this->getMock('Assetic\Tree\NodeInterface');
        $this->visitor->$method($node);
    }

    /**
     * @test
     * @dataProvider provideMethods
     */
    public function shouldReturnAsset($method)
    {
        $asset = $this->getMock('Assetic\Asset\AssetInterface');
        $this->assertInstanceOf('Assetic\Asset\AssetInterface', $this->visitor->$method($asset));
    }

    public function provideMethods()
    {
        return array(
            array('enter'),
            array('leave'),
        );
    }
}

class AbstractAssetVisitorStub extends AbstractAssetVisitor
{
}
