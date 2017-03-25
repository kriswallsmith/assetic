<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\MustacheFilter;

class MustacheFilterTest extends \PHPUnit_Framework_TestCase
{
    protected $escapedTemplate = '"<div class=\\"test\\">test {{ template }}<\/div>"';

    protected function setUp()
    {
    }

    public function testValidTemplateExtension()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/mustache/test.js.html');
        $asset->load();

        $filter = new MustacheFilter('.js.html');
        $filter->filterDump($asset);

        $this->assertEquals("views.test = {$this->escapedTemplate};\n", $asset->getContent());
    }

    public function testInvelidTemplateExtension()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/mustache/test.js');
        $asset->load();

        $filter = new MustacheFilter('.js.html');
        $filter->filterDump($asset);

        $this->assertEquals('', $asset->getContent());
    }

    public function testNamespace()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/mustache/test.js.html');
        $asset->load();

        $filter = new MustacheFilter('.js.html', 'test.namespace');
        $filter->filterDump($asset);

        $this->assertEquals("test.namespace.test = {$this->escapedTemplate};\n", $asset->getContent());
    }

    public function testRootFolder()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/mustache/subfolder/test.js.html', array(), __DIR__.'/fixtures/mustache');
        $asset->load();

        $filter = new MustacheFilter('.js.html', 'views', 'subfolder');
        $filter->filterDump($asset);

        $this->assertEquals("views.test = {$this->escapedTemplate};\n", $asset->getContent());
    }

    public function testSubFolder()
    {
        $asset = new FileAsset(__DIR__.'/fixtures/mustache/subfolder/test.js.html', array(), __DIR__.'/fixtures/mustache');
        $asset->load();

        $filter = new MustacheFilter('.js.html', 'views');
        $filter->filterDump($asset);

        $this->assertEquals("views.subfolder_test = {$this->escapedTemplate};\n", $asset->getContent());
    }
}
