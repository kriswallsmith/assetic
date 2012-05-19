<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Tree;

use Assetic\Tree\Node;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    private $node;

    protected function setUp()
    {
        $this->node = new Node();
    }

    protected function tearDown()
    {
        unset($this->node);
    }

    /**
     * @test
     */
    public function shouldSetAndGetAttributes()
    {
        $this->node->setAttribute('foo', 'bar');
        $this->assertEquals('bar', $this->node->getAttribute('foo'));
    }

    /**
     * @test
     */
    public function shouldReturnDefaultAttributeValue()
    {
        $this->assertEquals(123, $this->node->getAttribute('foo', 123));
    }

    /**
     * @test
     * @dataProvider provideInvalidAttributeValues
     * @expectedException InvalidArgumentException
     */
    public function shouldErrorOnInvalidAttributeValue($value)
    {
        $this->node->setAttribute('foo', $value);
    }

    public function provideInvalidAttributeValues()
    {
        return array(
            array((object) array()),
            array(array((object) array())),
        );
    }

    /**
     * @test
     */
    public function shouldReturnAllAttributes()
    {
        $this->node->setAttribute('foo', 'bar');
        $this->node->setAttribute('bar', 'foo');

        $this->assertEquals(array('foo' => 'bar', 'bar' => 'foo'), $this->node->getAttributes());
    }

    /**
     * @test
     */
    public function shouldManageChildParent()
    {
        $child = $this->getMock('Assetic\Tree\NodeInterface');

        $child->expects($this->at(0))
            ->method('setParent')
            ->with($this->node);
        $child->expects($this->at(1))
            ->method('setParent')
            ->with(null);

        $this->node->setChildren(array($child));
        $this->node->setChildren(array());
    }

    /**
     * @test
     */
    public function shouldRemoveValidChild()
    {
        $child = $this->getMock('Assetic\Tree\NodeInterface');

        $child->expects($this->at(0))
            ->method('setParent')
            ->with($this->node);
        $child->expects($this->at(1))
            ->method('setParent')
            ->with(null);

        $this->node->setChildren(array($child));
        $this->node->removeChild($child);

        $this->assertCount(0, $this->node->getChildren());
    }

    /**
     * @test
     */
    public function shouldIgnoreInvalidChildRemove()
    {
        $child = $this->getMock('Assetic\Tree\NodeInterface');
        $invalid = $this->getMock('Assetic\Tree\NodeInterface');

        $this->node->setChildren(array($child));
        $this->node->removeChild($invalid);

        $this->assertCount(1, $this->node->getChildren());
    }
}
