<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\AutoprefixerFilter;

/**
 * @group integration
 */
class AutoprefixerFilterTest extends FilterTestCase
{
    /**
     * @var AutoprefixerFilter
     */
    private $filter;

    protected function setUp()
    {
        $autoprefixerBin = $this->findExecutable('autoprefixer', 'AUTOPREFIXER_BIN');

        if (!$autoprefixerBin) {
            $this->markTestSkipped('Unable to find `autoprefixer` executable.');
        }

        $this->filter = new AutoprefixerFilter($autoprefixerBin);
    }

    public function testFilterLoad()
    {
        $input = <<<CSS
a {
  display: flex;
}
CSS;
        //TODO in some point of future this test will fail. Update test when new versions come out?
        $expected = <<<CSS
a {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
}
CSS;

        $asset = new StringAsset($input);
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent());
    }

    public function testReallyOldBrowsers()
    {
        $input = <<<CSS
img {
  border-radius: 10px;
}
CSS;
        $expected = <<<CSS
img {
  -moz-border-radius: 10px;
       border-radius: 10px;
}
CSS;

        $asset = new StringAsset($input);
        $asset->load();

        $this->filter->setBrowsers(array('ff 3'));
        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent());
    }

    public function testAddBrowser()
    {
        $input = <<<CSS
img {
  border-radius: 10px;
}
CSS;
        $expected = <<<CSS
img {
  -moz-border-radius: 10px;
       border-radius: 10px;
}
CSS;

        $asset = new StringAsset($input);
        $asset->load();

        $this->filter->addBrowser('ff 3');
        $this->filter->filterLoad($asset);

        $this->assertEquals($expected, $asset->getContent());
    }
}
