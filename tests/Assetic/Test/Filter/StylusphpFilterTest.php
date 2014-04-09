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
use Assetic\Filter\StylusphpFilter;

/**
 * @group integration
 * @author Linus Unnebäck
 */
class StylusphpFilterTest extends FilterTestCase
{
    private $filter;

    protected function setUp()
    {
        $this->filter = new StylusphpFilter();
    }

    public function testFilterLoad()
    {
        $asset = new StringAsset("html, body\n  margin 0\n  color #333");
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals("html, body {\n\tmargin: 0;\n\tcolor: #333;\n}\n", $asset->getContent(), '->filterLoad() parses the content');
    }

    public function testFilterNestedSelectors()
    {
        $asset = new StringAsset("body\n  color #333\n  p\n    color black");
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertEquals("body {\n\tcolor: #333;\n}\nbody p {\n\tcolor: black;\n}\n", $asset->getContent(), '->filterLoad() parses the content');
    }

}
