<?php

namespace Assetic\Test\Filter\GoogleClosure;

use Assetic\Asset\StringAsset;
use Assetic\Filter\GoogleClosure\CompilerApiFilter;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CompilerApiFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group functional
     */
    public function testRoundTrip()
    {
        $input = <<<EOF
function foo(bar) {
    var foo = 'foo';
    return foo + bar;
}
alert(foo("bar"));

EOF;

        $expected = 'function foo(a){return"foo"+a}alert(foo("bar"));';

        $asset = new StringAsset($input);
        $asset->load();

        $filter = new CompilerApiFilter(new Browser());
        $filter->filterDump($asset);

        $this->assertEquals($expected, $asset->getContent());
    }
}
