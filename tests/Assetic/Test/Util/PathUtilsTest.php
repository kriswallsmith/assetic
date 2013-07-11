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

use Assetic\Util\PathUtils;

class PathUtilsTest extends \PHPUnit_Framework_TestCase
{
    public function testResolvePath()
    {
        $template = '{foo}bar';
        $vars = array('foo');
        $values = array('foo' => 'foo');

        $this->assertEquals('foobar', PathUtils::resolvePath($template, $vars, $values));
    }
}
