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

use Assetic\Asset\FileAsset;
use Assetic\Asset\StringAsset;
use Assetic\Filter\LessphpFilter;

/**
 * @group integration
 */
class LessphpFilterTest extends LessFilterTest
{
    protected function setUp()
    {
        if (!isset($_SERVER['LESSPHP'])) {
            $this->markTestSkipped('No lessphp configuration.');
        }

        $this->filter = new LessphpFilter();
    }
}
