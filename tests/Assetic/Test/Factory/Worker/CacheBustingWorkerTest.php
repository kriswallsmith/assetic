<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Factory\Worker;

use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;

class CacheBustingWorkerTest extends \PHPUnit_Framework_TestCase
{
    private $worker;
    private $factory;

    protected function setUp()
    {
        $am = $this->getMock('Assetic\\AssetManager');
        $fm = $this->getMock('Assetic\\FilterManager');

        $this->worker = new CacheBustingWorker();

        $this->factory = new AssetFactory(__DIR__ . '/..');
        $this->factory->setAssetManager($am);
        $this->factory->setFilterManager($fm);
        $this->factory->addWorker($this->worker);
    }


    public function testGenerateUniqueAssetNameByContent()
    {
        $this->worker->setStrategy(CacheBustingWorker::STRATEGY_CONTENT);

        $filename = 'Resource/Fixtures/css/style.css';
        $filepath = __DIR__ . '/../' . $filename;

        $originalContent = file_get_contents($filepath);

        file_put_contents($filepath, 'body{color:#444;background:#eee;}');
        $asset = $this->factory->createAsset(array($filename));
        $targetPath1 = $asset->getTargetPath();

        file_put_contents($filepath, $originalContent);
        $asset = $this->factory->createAsset(array($filename));
        $targetPath2 = $asset->getTargetPath();

        $this->assertNotEquals($targetPath2, $targetPath1);
    }

    public function testGenerateUniqueAssetNameByModificationTime()
    {
        $this->worker->setStrategy(CacheBustingWorker::STRATEGY_MODIFICATION);

        $filename = 'Resource/Fixtures/css/style.css';
        $filepath = __DIR__ . '/../' . $filename;

        $asset = $this->factory->createAsset(array($filename));
        $this->factory->addWorker(new CacheBustingWorker('modification'));
        $targetPath1 = $asset->getTargetPath();

        sleep(1);
        touch($filepath);
        clearstatcache();

        $asset = $this->factory->createAsset(array($filename));
        $targetPath2 = $asset->getTargetPath();

        $this->assertNotEquals($targetPath2, $targetPath1);
    }
}
