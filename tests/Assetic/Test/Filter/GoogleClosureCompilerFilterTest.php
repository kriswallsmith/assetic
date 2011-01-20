<?php

namespace Assetic\Test\Filter;

use Assetic\Asset\Asset;
use Assetic\Filter\GoogleClosureCompilerFilter;
use Buzz\Browser;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class GoogleClosureCompilerFilterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Buzz\\Browser')) {
            $this->markTestSkipped('Buzz is not available.');
        }
    }

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

        $asset = new Asset($input);
        $asset->load();

        $filter = new GoogleClosureCompilerFilter(new Browser());
        $filter->filterDump($asset);

        $this->assertEquals($expected, $asset->getBody());
    }
}
