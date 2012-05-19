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

use Assetic\Tree\Comparator;

class ComparatorTest extends \PHPUnit_Framework_TestCase
{
    private $comparator;

    protected function setUp()
    {
        $this->comparator = new Comparator();
    }

    protected function tearDown()
    {
        unset($this->comparator);
    }

    /**
     * @test
     */
    public function shouldDetectAttributeDifferences()
    {
        $a = $this->getMock('Assetic\Tree\NodeInterface');
        $b = $this->getMock('Assetic\Tree\NodeInterface');

        $a->expects($this->any())
            ->method('getAttributes')
            ->will($this->returnValue(array('a' => 'a')));
        $b->expects($this->any())
            ->method('getAttributes')
            ->will($this->returnValue(array('b' => 'b')));

        $this->assertFalse($this->comparator->compare($a, $b));
    }

    /**
     * @test
     */
    public function shouldDetectChildCountDifferences()
    {
        $a = $this->getMock('Assetic\Tree\NodeInterface');
        $b = $this->getMock('Assetic\Tree\NodeInterface');
        $child = $this->getMock('Assetic\Tree\NodeInterface');

        $a->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue(array($child)));
        $b->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue(array($child, $child)));

        $this->assertFalse($this->comparator->compare($a, $b));
    }

    /**
     * @test
     */
    public function shouldDetectChildKeyDifferences()
    {
        $a = $this->getMock('Assetic\Tree\NodeInterface');
        $b = $this->getMock('Assetic\Tree\NodeInterface');
        $child = $this->getMock('Assetic\Tree\NodeInterface');

        $a->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue(array('a' => $child, 'b' => $child)));
        $b->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue(array('b' => $child, 'a' => $child)));

        $this->assertFalse($this->comparator->compare($a, $b));
    }

    /**
     * @test
     */
    public function shouldDetectChildDifferences()
    {
        $a = $this->getMock('Assetic\Tree\NodeInterface');
        $b = $this->getMock('Assetic\Tree\NodeInterface');
        $child = $this->getMock('Assetic\Tree\NodeInterface');

        $a->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue(array($child)));
        $b->expects($this->any())
            ->method('getChildren')
            ->will($this->returnValue(array($child)));
        $child->expects($this->any())
            ->method('getAttributes')
            ->will($this->onConsecutiveCalls(array('a' => 'a'), array('b' => 'b')));

        $this->assertFalse($this->comparator->compare($a, $b));
    }
}
