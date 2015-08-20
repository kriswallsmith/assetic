<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\MMMinifyFilter;

/**
 * @group integration
 */
class MMMinifyFilterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('MatthiasMullie\\Minify\\Minify')) {
            $this->markTestSkipped('matthiasmullie/minify is not installed.');
        }
    }

    /**
     * MinifyFilter::getMinifier is protected and I want to keep it that way
     * (it's not meant for public consumption)
     *
     * However, that's pretty much the only implementation that's worthy of
     * being tested here (minification is already tested in the library itself),
     * so I'll use reflection to get access to the result of that method.
     *
     * @param  FileAsset $asset
     * @return mixed
     */
    protected function getMinifier(FileAsset $asset)
    {
        $filter = new MMMinifyFilter();
        $object = new \ReflectionObject($filter);
        $method = $object->getMethod('getMinifier');
        $method->setAccessible(true);

        return $method->invoke($filter, $asset);
    }

    public function testDetectJSFromFilename()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/mmminify/js.js');
        $asset->load();

        $minifier = $this->getMinifier($asset);

        $this->assertInstanceOf('MatthiasMullie\\Minify\\JS', $minifier);
    }

    public function testDetectCSSFromFilename()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/mmminify/css.css');
        $asset->load();

        $minifier = $this->getMinifier($asset);

        $this->assertInstanceOf('MatthiasMullie\\Minify\\CSS', $minifier);
    }

    public function testDetectJSFromContent()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/mmminify/js.txt');
        $asset->load();

        $minifier = $this->getMinifier($asset);

        $this->assertInstanceOf('MatthiasMullie\\Minify\\JS', $minifier);
    }

    public function testDetectCSSFromContent()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/mmminify/css.txt');
        $asset->load();

        $minifier = $this->getMinifier($asset);

        $this->assertInstanceOf('MatthiasMullie\\Minify\\CSS', $minifier);
    }

    public function testRelativeCSSSourceUrlImportImports()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/cssmin/main.css');
        $asset->load();

        $filter = new MMMinifyFilter(__DIR__.'/fixtures/cssmin');
        $filter->filterDump($asset);

        $this->assertEquals('body{color:white}body{background:black}', $asset->getContent());
    }

    /**
     * This is similar to other minifier test's testRelativeJSSourceUrlImportImports
     */
    public function testCommonJSSnippet()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/jsmin/js.js');
        $asset->load();

        $filter = new MMMinifyFilter();
        $filter->filterDump($asset);

        $this->assertEquals('var a="abc";var bbb="u"', $asset->getContent());
    }
}
