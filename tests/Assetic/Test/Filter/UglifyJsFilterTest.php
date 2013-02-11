<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\UglifyJsFilter;
use Symfony\Component\Process\ProcessBuilder;

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

        // verify uglifyjs version
        $pb = new ProcessBuilder($nodeBin ? array($nodeBin, $uglifyjsBin) : array($uglifyjsBin));
        $pb->add('--version');
        if (isset($_SERVER['NODE_PATH'])) {
            $pb->setEnv('NODE_PATH', $_SERVER['NODE_PATH']);
        }
        if (0 === $pb->getProcess()->run()) {
            $this->markTestSkipped('Incorrect version of UglifyJs');
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
 */(function(){function t(e){return r.push(e),e}var e=new Array(1,2,3,4),t=Array(a,b,c),n=new Array(5),r=new Array(a),e=function(e){return e};e("abc123"),t("abc123")})();
JS;
        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testUnsafeUglify()
    {
        $this->filter->setUnsafe(true);
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
/**
 * Copyright
 */(function(){function t(e){return r.push(e),e}var e=[1,2,3,4],t=[a,b,c],n=Array(5),r=Array(a),e=function(e){return e};e("abc123"),t("abc123")})();
JS;
        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testBeautifyUglify()
    {
        $this->filter->setBeautify(true);
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
/**
 * Copyright
 */(function() {
    function t(e) {
        return r.push(e), e;
    }
    var e = new Array(1, 2, 3, 4), t = Array(a, b, c), n = new Array(5), r = new Array(a), e = function(e) {
        return e;
    };
    e("abc123"), t("abc123");
})();
JS;

        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testNoMangleUglify()
    {
        $this->filter->setMangle(false);
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
/**
 * Copyright
 */(function(){function bar(foo){return var2.push(foo),foo}var foo=new Array(1,2,3,4),bar=Array(a,b,c),var1=new Array(5),var2=new Array(a),foo=function(var1){return var1};foo("abc123"),bar("abc123")})();
JS;

        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testNoCopyrightUglify()
    {
        $this->filter->setNoCopyright(true);
        $this->filter->filterDump($this->asset);

        $expected = '(function(){function t(e){return r.push(e),e}var e=new Array(1,2,3,4),t=Array(a,b,c),n=new Array(5),r=new Array(a),e=function(e){return e};e("abc123"),t("abc123")})();';
        $this->assertEquals($expected, $this->asset->getContent());
    }
}
