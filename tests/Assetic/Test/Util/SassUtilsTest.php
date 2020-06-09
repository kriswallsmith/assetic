<?php namespace Assetic\Test\Util;

use PHPUnit\Framework\TestCase;
use Assetic\Util\SassUtils;

class SassUtilsTest extends TestCase
{
    public function testExtractImports()
    {

        $content = <<<CSS
// @import 'not_needed.css';
//@import "not_needed.css";
body{} // @import 'nod_needed.css';
@import 'custom.css';
@import "common.css" screen, projection;
body { background: url(../images/bg.gif); }
CSS;

        $expected = array('common.css', 'custom.css');
        $actual = SassUtils::extractImports($content);

        $this->assertEquals($expected, array_intersect($expected, $actual), '::extractImports() returns all expected URLs');
        $this->assertEquals([], array_diff($actual, $expected), '::extractImports() does not return unexpected URLs');
    }
}
