<?php

namespace Assetic\Test;

use Assetic\AssetManager;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class AssetManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $am = new AssetManager();
        $this->assertInstanceOf('Assetic\\Filter\\Filterable', $am, 'AssetManager implements Filterable');
    }
}
