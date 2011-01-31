<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter\Yui;

use Assetic\Asset\StringAsset;
use Assetic\Filter\Yui\JsCompressorFilter;

class JsCompressorFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new JsCompressorFilter('/path/to/jar');
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'JsCompressorFilter implements FilterInterface');
    }

    /**
     * @group functional
     */
    public function testFilterDump()
    {
        if (!isset($_SERVER['YUI_COMPRESSOR_PATH'])) {
            $this->markTestSkipped('There is no YUI_COMPRESSOR_PATH environment variable.');
        }

        $source = <<<JAVASCRIPT
(function() {

var asdf = 'asdf';
var qwer = 'qwer';

if (asdf.indexOf(qwer)) {
    alert("That's not possible!");
} else {
    alert("Boom.");
}

})();

JAVASCRIPT;

        $expected = <<<JAVASCRIPT
(function(){var a="asdf";var b="qwer";if(a.indexOf(b)){alert("That's not possible!")}else{alert("Boom.")}})();
JAVASCRIPT;

        $asset = new StringAsset($source);
        $asset->load();

        $filter = new JsCompressorFilter($_SERVER['YUI_COMPRESSOR_PATH']);
        $filter->filterDump($asset);

        $this->assertEquals($expected, $asset->getContent(), '->filterDump()');
    }
}
