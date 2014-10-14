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
        $content = <<<CSS
body { background: url(../images/bg1.gif); }
body { background: url( ../images/bg2.gif ); }
body { background: url('../images/bg3.gif'); }
body { background: url("../images/bg4.gif"); }
body { background: url( '../images/bg5.gif' ); }
body { background: url( "../images/bg6.gif" ); }
body { background: url(/images/bg7.gif); }
body { background: url(http://www.example.com/images/bg8.gif); }
body {
\tbackground: url(
\t\t"../images/bg9.gif"
\t);
}
CSS;

        $expected = array(
            '../images/bg1.gif',
            '../images/bg2.gif',
            '../images/bg3.gif',
            '../images/bg4.gif',
            '../images/bg5.gif',
            '../images/bg6.gif',
            '/images/bg7.gif',
            'http://www.example.com/images/bg8.gif',
            '../images/bg9.gif'
        );

        $actual = array();
        CssUtils::filterUrls($content, function($match) use(& $actual) {
            $actual[] = $match['url'];
        });

        $this->assertEquals($expected, $actual);
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
