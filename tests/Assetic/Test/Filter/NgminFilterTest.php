<?php
/**
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;


use Assetic\Asset\FileAsset;
use Assetic\Filter\NgminFilter;

class NgminFilterTest extends FilterTestCase
{
    private $asset;
    private $filter;

    public function setUp()
    {
        $ngminBin = $this->findExecutable('ngmin', 'NGMIN_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');
        if (!$ngminBin) {
            $this->markTestSkipped('Unable to find `uglifyjs` executable.');
        }


        $this->asset = new FileAsset(__DIR__.'/fixtures/ngmin/script.js');
        $this->asset->load();

        $this->filter = new NgminFilter($ngminBin, $nodeBin);
    }

    protected function tearDown()
    {
        $this->asset = null;
        $this->filter = null;
    }

    public function testFilterDump()
    {
        $this->filter->filterDump($this->asset);

        $script = <<<EOF
angular.module('app').controller('MainCtrl', [
  '\$scope',
  function (\$scope) {
  }
]);
EOF;
        $this->assertEquals($script, $this->asset->getContent());
    }

}
