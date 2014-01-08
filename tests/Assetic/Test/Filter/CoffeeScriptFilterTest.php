<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\CoffeeScriptFilter;

/**
 * @group integration
 */
class CoffeeScriptFilterTest extends FilterTestCase
{
    private $filter;

    protected function setUp()
    {
        $coffeeBin = $this->findExecutable('coffee', 'COFFEE_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');

        if (!$coffeeBin) {
            $this->markTestSkipped('Unable to find `coffee` executable.');
        }

        $this->filter = new CoffeeScriptFilter($coffeeBin, $nodeBin);
    }

    public function testFilterLoad()
    {
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

        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $this->clean($asset->getContent()));
    }

    public function testBare()
    {
        $expected = <<<JAVASCRIPT
var square;

square = function(x) {
  return x * x;
};

JAVASCRIPT;
        $asset = new StringAsset('square = (x) -> x * x');
        $asset->load();

        $this->filter->setBare(true);
        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $this->clean($asset->getContent()));
    }

    private function clean($js)
    {
        return preg_replace('~^//.*\n\s*~m', '', $js);
    }
}
