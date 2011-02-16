<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\CoffeeScriptFilter;

class CoffeeScriptFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group integration
     */
    public function testFilterLoad()
    {
        if (!isset($_SERVER['COFFEE_PATH']) || !isset($_SERVER['NODE_BIN'])) {
            $this->markTestSkipped('There is no COFFEE_PATH or NODE_BIN environment variable.');
        }

        $expected = <<<JAVASCRIPT
(function() {
  var square;
  square = function(x) {
    return x * x;
  };
}).call(this);

JAVASCRIPT;

        $asset = new StringAsset('square = (x) -> x * x');
        $asset->load();

        $filter = new CoffeeScriptFilter($_SERVER['COFFEE_PATH'], $_SERVER['NODE_BIN']);
        $filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent());
    }
}
