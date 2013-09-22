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

use Assetic\Asset\StringAsset;
use Assetic\Util\PathUtils;

class PathUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getResolveRelative
     */
    public function testResolveRelative($from, $to, $expected)
    {
        $actual = PathUtils::resolveRelative($from, $to);

        $this->assertEquals($expected, $actual, sprintf('Relative from "%s" to "%s" should be "%s"', $from, $to, $expected));
    }

    public function getResolveRelative()
    {
        return array(
            array('foo',     'foo',     ''),
            array('foo',     'bar',     ''),
            array('foo/bar', 'foo/baz',     ''),
            array('foo/bar', 'bar/baz',     '../foo/'),
            array('../vendor/foo', 'bar/baz',     '../../vendor/'),
        );
    }

    /**
     * @dataProvider getResolveUrl
     */
    public function testResolveUrl($sourceRoot, $targetPath, $url, $expected)
    {
        $asset = new StringAsset('foo', array(), $sourceRoot);
        $asset->setTargetPath($targetPath);

        $actual = PathUtils::resolveUrl($asset, $url);

        $this->assertEquals($expected, $actual, sprintf('Resolving URL "%s" with source "%s" and target "%s" should be "%s".', $url, $sourceRoot, $targetPath, $expected));
    }

    public function getResolveUrl()
    {
        return array(
            array('/var/www/web', 'assets/foo', '../../vendor/foo/bar?x=3#foo', '/var/www/vendor/foo/bar'),
            array('web', 'assets/foo', '../../vendor/foo/bar', 'vendor/foo/bar'),
            array('/var/www/web', 'foo', 'bar/baz', '/var/www/web/bar/baz'),
        );
    }

    /**
     * @dataProvider getResolveUps
     */
    public function testResolveUps($source, $expected)
    {
        $actual = PathUtils::resolveUps($source, $expected);
        $this->assertEquals($expected, $actual, sprintf('Resolving ups for "%s" should be "%s".', $source, $expected));
    }

    public function getResolveUps()
    {
        return array(
            array('foo/bar', 'foo/bar'),
            array('/var/www/foo/bar/../baz', '/var/www/foo/baz'),
            array('foo/bar/../baz/../../bar', 'bar'),
        );
    }

    /**
     * @dataProvider getRemoveQueryString
     */
    public function testRemoveQueryString($source, $expected)
    {
        $actual = PathUtils::removeQueryString($source, $expected);
        $this->assertEquals($expected, $actual, sprintf('Removing query string from "%s" should be "%s".', $source, $expected));
    }

    public function getRemoveQueryString()
    {
        return array(
            array('foo', 'foo'),
            array('foo?bar', 'foo'),
            array('foo?bar=2&baz=3', 'foo'),
            array('foo?bar=2&baz=3#bar', 'foo#bar'),
        );
    }

    /**
     * @dataProvider getRemoveAnchor
     */
    public function testRemoveAnchor($source, $expected)
    {
        $actual = PathUtils::removeAnchor($source, $expected);
        $this->assertEquals($expected, $actual, sprintf('Removing anchor from "%s" should be "%s".', $source, $expected));
    }

    public function getRemoveAnchor()
    {
        return array(
            array('foo', 'foo'),
            array('foo?bar', 'foo?bar'),
            array('foo?bar#bar', 'foo?bar'),
            array('foo#bar?bar', 'foo'),
            array('foo#bar', 'foo'),
        );
    }

    /**
     * @dataProvider getIsPath
     */
    public function testIsPath($expected, $source)
    {
        $actual = PathUtils::isPath($source, $expected);
        $this->assertEquals($expected, $actual, sprintf('"%s" should%s be a path.', $source, $expected ? '' : ' not'));
    }

    public function getIsPath()
    {
        return array(
            array(true, 'foo'),
            array(true, 'foo/bar'),
            array(true, '/foo/bar'),
            array(false, 'http://foo/bar'),
            array(false, 'data:image'),
        );
    }

    /**
     * @dataProvider getIsRootPath
     */
    public function testIsRootPath($expected, $source)
    {
        $actual = PathUtils::isRootPath($source, $expected);
        $this->assertEquals($expected, $actual, sprintf('"%s" should%s be a path.', $source, $expected ? '' : ' not'));
    }

    public function getIsRootPath()
    {
        return array(
            array(true, '/foo/bar'),
            array(false, 'foo'),
            array(false, 'foo/bar'),
            array(false, 'http://foo/bar'),
            array(false, 'data:image'),
        );
    }
}
