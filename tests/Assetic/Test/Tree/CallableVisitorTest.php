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

use Assetic\Tree\CallableVisitor;

class CallableVisitorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCallEnterCallable()
    {
        $node = $this->getMock('Assetic\Tree\NodeInterface');

        $node->expects($this->once())
            ->method('setAttribute')
            ->with('called', true);

        $visitor = new CallableVisitor(function($node) {
            $node->setAttribute('called', true);
            return $node;
        });

        $visitor->enter($node);
    }

    /**
     * @test
     */
    public function shouldCallLeaveCallable()
    {
        $node = $this->getMock('Assetic\Tree\NodeInterface');

        $node->expects($this->once())
            ->method('setAttribute')
            ->with('called', true);

        $visitor = new CallableVisitor(null, function($node) {
            $node->setAttribute('called', true);
            return $node;
        });

        $visitor->leave($node);
    }

    /**
     * @test
     */
    public function shouldAllowNullCallables()
    {
        $visitor = new CallableVisitor(null, null);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldErrorOnInvalidEnterCallable()
    {
        $visitor = new CallableVisitor('asdf');
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function shouldErrorOnInvalidLeaveCallable()
    {
        $visitor = new CallableVisitor(null, 'asdf');
    }
}
