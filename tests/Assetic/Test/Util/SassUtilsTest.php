<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2015 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Util;

use Assetic\Util\SassUtils;

class SassUtilsTest extends \PHPUnit_Framework_TestCase
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
        $this->assertEquals(array(), array_diff($actual, $expected), '::extractImports() does not return unexpected URLs');
    }
}
