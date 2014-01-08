<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Util;

use Assetic\Util\CssUtils;

class CssUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterUrls()
    {
        $content = 'body { background: url(../images/bg.gif); }';

        $matches = array();
        $actual = CssUtils::filterUrls($content, function($match) use(& $matches) {
            $matches[] = $match['url'];
        });

        $this->assertEquals(array('../images/bg.gif'), $matches);
    }

    public function testExtractImports()
    {
        // These don't work yet (todo):
        // @import url("fineprint.css") print;
        // @import url("bluish.css") projection, tv;
        // @import url('landscape.css') screen and (orientation:landscape);

        $content = <<<CSS
@import 'custom.css';
@import "common.css" screen, projection;
body { background: url(../images/bg.gif); }
CSS;

        $expected = array('common.css', 'custom.css');
        $actual = CssUtils::extractImports($content);

        $this->assertEquals($expected, array_intersect($expected, $actual), '::extractImports() returns all expected URLs');
        $this->assertEquals(array(), array_diff($actual, $expected), '::extractImports() does not return unexpected URLs');
    }

    public function testFilterCommentless()
    {
        $content = 'A/*B*/C/*D*/E';

        $filtered = '';
        $result = CssUtils::filterCommentless($content, function($part) use(& $filtered) {
            $filtered .= $part;
            return $part;
        });

        $this->assertEquals('ACE', $filtered);
        $this->assertEquals($content, $result);
    }
}
