<?php namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\UglifyJsFilter;
use Symfony\Component\Process\Process;

/**
 * @group integration
 */
class UglifyJsFilterTest extends FilterTestCase
{
    private $asset;
    private $filter;

    protected function setUp(): void
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

    protected function tearDown(): void
    {
        $this->asset = null;
        $this->filter = null;
    }

    public function testUglify()
    {
        $this->filter->setComments('/Copyright/');
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
/**
 * Copyright
 */
if(typeof DEBUG==="undefined"){DEBUG=true}if(typeof FOO==="undefined"){FOO=1}(function(){var foo=new Array(FOO,2,3,4);var bar=Array(a,b,c);var var1=new Array(5);var var2=new Array(a);function bar(foo){var2.push(foo);return foo}var foo=function(var1){DEBUG&&console.log("hellow world");return var1};foo("abc123");bar("abc123")})();
JS;

        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testDefines()
    {
        $this->filter->setDefines(array('DEBUG=false'));
        $this->filter->setComments('/Copyright/');
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
/**
 * Copyright
 */
"undefined"==typeof FOO&&(FOO=1),function(){new Array(FOO,2,3,4);var bar=Array(a,b,c),var2=(new Array(5),new Array(a));function bar(foo){return var2.push(foo),foo}bar("abc123")}();
JS;
        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testMutipleDefines()
    {
        $this->filter->setDefines(array('DEBUG=false', 'FOO=2'));
        $this->filter->setComments('/Copyright/');
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
/**
 * Copyright
 */
(function(){new Array(2,2,3,4);var bar=Array(a,b,c),var2=(new Array(5),new Array(a));function bar(foo){return var2.push(foo),foo}bar("abc123")})();
JS;
        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testUnsafeUglify()
    {
        $this->filter->setUnsafe(true);
        $this->filter->setComments('/Copyright/');
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
/**
 * Copyright
 */
if(typeof DEBUG==="undefined"){DEBUG=true}if(typeof FOO==="undefined"){FOO=1}(function(){var foo=new Array(FOO,2,3,4);var bar=Array(a,b,c);var var1=new Array(5);var var2=new Array(a);function bar(foo){var2.push(foo);return foo}var foo=function(var1){DEBUG&&console.log("hellow world");return var1};foo("abc123");bar("abc123")})();
JS;
        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testBeautifyUglify()
    {
        $this->filter->setBeautify(true);
        $this->filter->setComments('/Copyright/');
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
/**
 * Copyright
 */
if (typeof DEBUG === "undefined") {
    DEBUG = true;
}

if (typeof FOO === "undefined") {
    FOO = 1;
}

(function() {
    var foo = new Array(FOO, 2, 3, 4);
    var bar = Array(a, b, c);
    var var1 = new Array(5);
    var var2 = new Array(a);
    function bar(foo) {
        var2.push(foo);
        return foo;
    }
    var foo = function(var1) {
        DEBUG && console.log("hellow world");
        return var1;
    };
    foo("abc123");
    bar("abc123");
})();
JS;

        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testNoMangleUglify()
    {
        $this->filter->setMangle(false);
        $this->filter->filterDump($this->asset);

        $expected = <<<JS
if(typeof DEBUG==="undefined"){DEBUG=true}if(typeof FOO==="undefined"){FOO=1}(function(){var foo=new Array(FOO,2,3,4);var bar=Array(a,b,c);var var1=new Array(5);var var2=new Array(a);function bar(foo){var2.push(foo);return foo}var foo=function(var1){DEBUG&&console.log("hellow world");return var1};foo("abc123");bar("abc123")})();
JS;

        $this->assertEquals($expected, $this->asset->getContent());
    }

    public function testNoCopyrightUglify()
    {
        $this->filter->setNoCopyright(true);
        $this->filter->filterDump($this->asset);

        $expected = 'if(typeof DEBUG==="undefined"){DEBUG=true}if(typeof FOO==="undefined"){FOO=1}(function(){var foo=new Array(FOO,2,3,4);var bar=Array(a,b,c);var var1=new Array(5);var var2=new Array(a);function bar(foo){var2.push(foo);return foo}var foo=function(var1){DEBUG&&console.log("hellow world");return var1};foo("abc123");bar("abc123")})();';
        $this->assertEquals($expected, $this->asset->getContent());
    }
}
