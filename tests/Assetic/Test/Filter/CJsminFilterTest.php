<?php

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\CJSMinFilter;

/**
 * Tests for CJSMinFilter
 *
 * @author Felix Yeung
 */
class CJSMinFilterTest extends FilterTestCase
{
    protected function setUp()
    {
        if (!$this->findExecutable('jsmin', 'JSMIN_BIN')) {
            $this->markTestSkipped('Unable to find `jsmin` executable.');
        }
    }

    public function testFilterMinifiesAsset()
    {
        $asset = new FileAsset(__DIR__ . '/fixtures/jsmin/js.js');
        $asset->load();

        $cjsminFilter = new CJSMinFilter();
        $cjsminFilter->filterDump($asset);

        $this->assertEquals(
            "\nvar a=\"abc\";;;var bbb=\"u\";",
            $asset->getContent()
        );
    }
}
