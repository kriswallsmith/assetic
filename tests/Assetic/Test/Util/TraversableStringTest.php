<?php namespace Assetic\Test\Util;

use PHPUnit\Framework\TestCase;
use Assetic\Util\TraversableString;

class TraversableStringTest extends TestCase
{
    public function testString()
    {
        $foo = new TraversableString('foo', array('foo', 'bar'));
        $this->assertEquals('foo', (string) $foo);
    }

    public function testArray()
    {
        $foo = new TraversableString('foo', array('foo', 'bar'));

        $values = [];
        foreach ($foo as $value) {
            $values[] = $value;
        }

        $this->assertEquals(array('foo', 'bar'), $values);
    }
}
