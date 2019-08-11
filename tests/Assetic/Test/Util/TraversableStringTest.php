<?php namespace Assetic\Test\Util;

use Assetic\Util\TraversableString;

class TraversableStringTest extends \PHPUnit_Framework_TestCase
{
    public function testString()
    {
        $foo = new TraversableString('foo', array('foo', 'bar'));
        $this->assertEquals('foo', (string) $foo);
    }

    public function testArray()
    {
        $foo = new TraversableString('foo', array('foo', 'bar'));

        $values = array();
        foreach ($foo as $value) {
            $values[] = $value;
        }

        $this->assertEquals(array('foo', 'bar'), $values);
    }
}
