<?php

namespace Assetic\Test\Filter\Yui;

use Assetic\Asset\Asset;
use Assetic\Filter\Yui\YuiCompressorJsFilter;

class YuiCompressorJsFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new YuiCompressorJsFilter('/path/to/jar');
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'YuiCompressorJsFilter implements FilterInterface');
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

        $asset = new Asset($source);
        $asset->load();

        $filter = new YuiCompressorJsFilter($_SERVER['YUI_COMPRESSOR_PATH']);
        $filter->filterDump($asset);

        $this->assertEquals($expected, $asset->getBody(), '->filterDump()');
    }
}
