<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\UglifyJsFilter;

/**
 * @group integration
 */
class UglifyJsFilterTest extends FilterTestCase
{
    private $asset;
    private $filter;

    protected function setUp()
    {
        $uglifyjsBin = $this->findExecutable('uglifyjs', 'UGLIFYJS_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');
        if (!$uglifyjsBin) {
            $this->markTestSkipped('Unable to find `uglifyjs` executable.');
        }

        $this->asset = new FileAsset(__DIR__.'/fixtures/uglifyjs/script.js');
        $this->asset->load();

        $this->filter = new UglifyJsFilter($uglifyjsBin, $nodeBin);
    }

    protected function tearDown()
    {
        $this->asset = null;
        $this->filter = null;
    }

    public function testUglify()
    {
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
/**
 * Copyright
 */function bar(e){return var2.push(e),e}var foo=new Array(1,2,3,4),bar=Array(a,b,c),var1=new Array(5),var2=new Array(a),foo=function(e){return e};
JS;
        $this->assertSame($expected, $this->asset->getContent());
    }

    public function testUnsafeUglify()
    {
        $this->filter->setUnsafe(true);
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
/**
 * Copyright
 */function bar(e){return var2.push(e),e}var foo=[1,2,3,4],bar=[a,b,c],var1=Array(5),var2=Array(a),foo=function(e){return e};
JS;
        $this->assertSame($expected, $this->asset->getContent());
    }

    public function testBeautifyUglify()
    {
        $this->filter->setBeautify(true);
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
/**
 * Copyright
 */function bar(e) {
    return var2.push(e), e;
}

var foo = new Array(1, 2, 3, 4), bar = Array(a, b, c), var1 = new Array(5), var2 = new Array(a), foo = function(e) {
    return e;
};
JS;

        $this->assertSame($expected, $this->asset->getContent());
    }

    public function testMangleUglify()
    {
        $this->filter->setMangle(true);
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
/**
 * Copyright
 */function bar(e){return var2.push(e),e}var foo=new Array(1,2,3,4),bar=Array(a,b,c),var1=new Array(5),var2=new Array(a),foo=function(e){return e};
JS;

        $this->assertSame($expected, $this->asset->getContent());
    }

    public function testNoCopyrightUglify()
    {
        $this->filter->setNoCopyright(true);
        $this->filter->filterDump($this->asset);

        $expected = 'function bar(e){return var2.push(e),e}var foo=new Array(1,2,3,4),bar=Array(a,b,c),var1=new Array(5),var2=new Array(a),foo=function(e){return e};';
        $this->assertSame($expected, $this->asset->getContent());
    }
}
