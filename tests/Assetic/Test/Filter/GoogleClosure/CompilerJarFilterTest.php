<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter\GoogleClosure;

use Assetic\Asset\StringAsset;
use Assetic\Filter\GoogleClosure\CompilerJarFilter;
use Assetic\Test\Filter\FilterTestCase;

/**
 * @group integration
 */
class CompilerJarFilterTest extends FilterTestCase
{
    private $filter;

    protected function setUp()
    {
        if (!$javaBin = $this->findExecutable('java', 'JAVA_BIN')) {
            $this->markTestSkipped('Unable to find `java` executable.');
        }

        if (!isset($_SERVER['CLOSURE_JAR'])) {
            $this->markTestSkipped('There is no CLOSURE_JAR environment variable.');
        }

        $this->filter = new CompilerJarFilter($_SERVER['CLOSURE_JAR'], $javaBin);
    }

    protected function tearDown()
    {
        $this->filter = null;
    }

    public function testCompile()
    {
        $input = <<<EOF
(function() {
function unused(){}
function foo(bar)
{
    var foo = 'foo';

    return foo + bar;
}
alert(foo("bar"));
})();
EOF;

        $expected = <<<EOF
(function(){alert("foobar")})();

EOF;

        $asset = new StringAsset($input);
        $asset->load();

        $this->filter->filterDump($asset);

        $this->assertEquals($expected, $asset->getContent());
    }

    public function testCompileEcma5()
    {
        $input = <<<EOF
(function() {
    var int = 123;
    console.log(int);
})();
EOF;

        $expected = <<<EOF
(function(){console.log(123)})();

EOF;

        $asset = new StringAsset($input);
        $asset->load();

        $this->filter->setLanguage(CompilerJarFilter::LANGUAGE_ECMASCRIPT5);
        $this->filter->filterDump($asset);

        $this->assertEquals($expected, $asset->getContent());
    }
}
