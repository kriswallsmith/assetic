<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Util;

use Assetic\Util\CssUtils;

class CssUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractImports()
    {
        // These don't work yet (todo):
        // @import url("fineprint.css") print;
        // @import url("bluish.css") projection, tv;
        // @import url('landscape.css') screen and (orientation:landscape);

        $content = <<<CSS
@import 'custom.css';
@import "common.css" screen, projection;
@import    "spaces.css";
@import url("url1.css");
@import url("url2.css");
@import   url('url3.css');
@import-once 'once.css';
@import-once    url("once-url.css");
body { background: url(../images/bg.gif); }
CSS;

        $expected = array('common.css', 'custom.css', 'spaces.css', 'url1.css', 'url2.css', 'url3.css', 'once.css', 'once-url.css');
        $actual = CssUtils::extractImports($content);

        $this->assertEquals($expected, array_intersect($expected, $actual), '::extractImports() returns all expected URLs');
        $this->assertEquals(array(), array_diff($actual, $expected), '::extractImports() does not return unexpected URLs');
    }
}
