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
use Assetic\Filter\UglifyJs2Filter;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @group integration
 */
class UglifyJs2FilterTest extends FilterTestCase
{
    private $asset;
    private $filter;

    protected function setUp()
    {
        $uglifyjsBin = $this->findExecutable('uglifyjs', 'UGLIFYJS2_BIN');
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
        if (0 !== $pb->getProcess()->run()) {
            $this->markTestSkipped('Incorrect version of UglifyJs');
        }

        $this->asset = new FileAsset(__DIR__.'/fixtures/uglifyjs/script.js');
        $this->asset->load();

        $this->filter = new UglifyJs2Filter($uglifyjsBin, $nodeBin);
    }

    protected function tearDown()
    {
        $this->asset = null;
        $this->filter = null;
    }

    public function testUglify()
    {
        $this->filter->filterDump($this->asset);

        $expected = '(function(){var foo=new Array(1,2,3,4);var bar=Array(a,b,c);var var1=new Array(5);var var2=new Array(a);function bar(foo){var2.push(foo);return foo}var foo=function(var1){return var1};foo("abc123");bar("abc123")})();';
        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testCompress()
    {
        $this->filter->setCompress(true);
        $this->filter->filterDump($this->asset);

        $expected = '(function(){function bar(foo){return var2.push(foo),foo}var foo=[1,2,3,4],bar=[a,b,c];Array(5);var var2=Array(a),foo=function(var1){return var1};foo("abc123"),bar("abc123")})();';
        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testMangle()
    {
        $this->filter->setMangle(true);
        $this->filter->filterDump($this->asset);

        $expected = '(function(){var r=new Array(1,2,3,4);var n=Array(a,b,c);var u=new Array(5);var e=new Array(a);function n(r){e.push(r);return r}var r=function(r){return r};r("abc123");n("abc123")})();';
        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testCompressAndMangle()
    {
        $this->filter->setCompress(true);
        $this->filter->setMangle(true);
        $this->filter->filterDump($this->asset);

        $expected = '(function(){function r(r){return u.push(r),r}var n=[1,2,3,4],r=[a,b,c];Array(5);var u=Array(a),n=function(r){return r};n("abc123"),r("abc123")})();';
        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testBeautify()
    {
        $this->filter->setBeautify(true);
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
(function() {
    var foo = new Array(1, 2, 3, 4);
    var bar = Array(a, b, c);
    var var1 = new Array(5);
    var var2 = new Array(a);
    function bar(foo) {
        var2.push(foo);
        return foo;
    }
    var foo = function(var1) {
        return var1;
    };
    foo("abc123");
    bar("abc123");
})();
JS;
        $this->assertEquals($expected, $this->asset->getContent());
    }
}
