<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter\GoogleClosure;

use Assetic\Asset\StringAsset;
use Assetic\Filter\GoogleClosure\CompilerJarFilter;

class CompilerJarFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group integration
     */
    public function testCompile()
    {
        if (!isset($_SERVER['GOOGLE_CLOSURE_COMPILER_PATH'])) {
            $this->markTestSkipped('There is no GOOGLE_CLOSURE_COMPILER_PATH environment variable.');
        }

        $input = <<<EOF
(function() {
function unused(){}
function foo(bar) {
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

        $filter = new CompilerJarFilter($_SERVER['GOOGLE_CLOSURE_COMPILER_PATH']);
        $filter->filterLoad($asset);
        $filter->filterDump($asset);

        $this->assertEquals($expected, $asset->getContent());
    }
}
