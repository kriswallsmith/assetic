<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Tree;

use Assetic\Tree\Traverser;

class TraverserTest extends \PHPUnit_Framework_TestCase
{
    private $traverser;

    protected function setUp()
    {
        $this->traverser = new Traverser();
    }

    protected function tearDown()
    {
        unset($this->traverser);
    }

    /**
     * @test
     */
    public function shouldVisitRootNode()
    {
        $visitor = $this->getMock('Assetic\Tree\VisitorInterface');
        $node = $this->getMock('Assetic\Tree\NodeInterface');

        $visitor->expects($this->once())
            ->method('enter')
            ->with($node)
            ->will($this->returnValue($node));
        $node->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue(array()));
        $visitor->expects($this->once())
            ->method('leave')
            ->with($node)
            ->will($this->returnValue($node));

        $this->traverser->addVisitor($visitor);
        $this->assertSame($node, $this->traverser->traverse($node));
    }

    /**
     * @test
     */
    public function shouldVisitChildNodes()
    {
        $visitor = $this->getMock('Assetic\Tree\VisitorInterface');
        $node = $this->getMock('Assetic\Tree\NodeInterface');
        $child = $this->getMock('Assetic\Tree\NodeInterface');

        $visitor->expects($this->exactly(2))
            ->method('enter')
            ->will($this->onConsecutiveCalls($node, $child));
        $node->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue(array($child)));
        $child->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue(array()));
        $visitor->expects($this->exactly(2))
            ->method('leave')
            ->will($this->onConsecutiveCalls($child, $node));
        $node->expects($this->once())
            ->method('setChild')
            ->with(0, $child);

        $this->traverser->addVisitor($visitor);
        $this->assertSame($node, $this->traverser->traverse($node));
    }

    /**
     * @test
     */
    public function shouldRemoveChildrenOnVisitorLeaveFalse()
    {
        $visitor = $this->getMock('Assetic\Tree\VisitorInterface');
        $node = $this->getMock('Assetic\Tree\NodeInterface');
        $child = $this->getMock('Assetic\Tree\NodeInterface');

        $visitor->expects($this->exactly(2))
            ->method('enter')
            ->will($this->onConsecutiveCalls($node, $child));
        $node->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue(array($child)));
        $child->expects($this->once())
            ->method('getChildren')
            ->will($this->returnValue(array()));
        $visitor->expects($this->exactly(2))
            ->method('leave')
            ->will($this->onConsecutiveCalls(false, $node));
        $node->expects($this->once())
            ->method('removeChild')
            ->with($child);

        $this->traverser->addVisitor($visitor);
        $this->assertSame($node, $this->traverser->traverse($node));
    }

    /**
     * @test
     */
    public function shouldTraverseWithoutVisitors()
    {
        $node = $this->getMock('Assetic\Tree\NodeInterface');
        $this->traverser->traverse($node);
    }
}
