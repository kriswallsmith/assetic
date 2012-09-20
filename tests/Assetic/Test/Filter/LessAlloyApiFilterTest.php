<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\LessAlloyApiFilter;

/**
 * @group integration
 */
class LessAlloyApiFilterTest extends LessFilterTest
{
    protected function setUp()
    {
        // @TODO Pull Alloy API server info from env.
        $this->filter = new LessAlloyApiFilter();
    }

    /**
     * @TODO Support loading a directory of files with API call.
     */
    public function testLoadPath()
    {
        $this->markTestSkipped('Less Alloy API load by path has not been implemented yet.');
    }
}
