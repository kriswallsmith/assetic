<?php

namespace Assetic\Test;

use Assetic\AssetManager;

class AssetManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $am = new AssetManager();
        $this->assertInstanceOf('Assetic\\Filter\\Filterable', $am, 'AssetManager implements Filterable');
    }
}
