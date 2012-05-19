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

use Assetic\Tree\RepeatingTraverser;

class RepeatingTraverserTest extends \PHPUnit_Framework_TestCase
{
    private $delegate;
    private $comparator;
    private $traverser;

    protected function setUp()
    {
        $this->delegate = $this->getMock('Assetic\Tree\TraverserInterface');
        $this->comparator = $this->getMock('Assetic\Tree\ComparatorInterface');
        $this->traverser = new RepeatingTraverser($this->delegate, $this->comparator);
    }

    protected function tearDown()
    {
        unset(
            $this->delegate,
            $this->comparator,
            $this->traverser
        );
    }

    /**
     * @test
     */
    public function shouldRepeatTraversal()
    {
        $node = $this->getMock('Assetic\Tree\NodeInterface');

        $this->delegate->expects($this->exactly(2))
            ->method('traverse')
            ->with($node)
            ->will($this->returnValue($node));
        $this->comparator->expects($this->exactly(2))
            ->method('compare')
            ->will($this->onConsecutiveCalls(false, true));

        $this->traverser->traverse($node);
    }
}
