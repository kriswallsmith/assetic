<?php namespace Assetic\Test\Asset;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Asset\AssetInterface;
use Assetic\Asset\GlobAsset;
use Assetic\Util\VarUtils;

class GlobAssetTest extends TestCase
{
    public function testInterface()
    {
        $asset = new GlobAsset(__DIR__.'/*.php');
        $this->assertInstanceOf(AssetInterface::class, $asset, 'Asset implements AssetInterface');
    }

    public function testIteration()
    {
        $assets = new GlobAsset(__DIR__.'/*.php');
        $this->assertGreaterThan(0, iterator_count($assets), 'GlobAsset initializes for iteration');
    }

    public function testRecursiveIteration()
    {
        $assets = new GlobAsset(__DIR__.'/*.php');
        $this->assertGreaterThan(0, iterator_count($assets), 'GlobAsset initializes for recursive iteration');
    }

    public function testGetLastModifiedType()
    {
        $assets = new GlobAsset(__DIR__.'/*.php');
        $this->assertIsInt($assets->getLastModified(), '->getLastModified() returns an integer');
    }

    public function testGetLastModifiedValue()
    {
        $assets = new GlobAsset(__DIR__.'/*.php');
        $this->assertLessThan(time(), $assets->getLastModified(), '->getLastModified() returns a file mtime');
    }

    public function testLoad()
    {
        $assets = new GlobAsset(__DIR__.'/*.php');
        $assets->load();

        $this->assertNotEmpty($assets->getContent(), '->load() loads contents');
    }

    public function testDump()
    {
        $assets = new GlobAsset(__DIR__.'/*.php');
        $this->assertNotEmpty($assets->dump(), '->dump() dumps contents');
    }

    public function testVariableInPath()
    {
        $globasset = new GlobAsset(__DIR__.'/*.php', [], null, array('testvar'));
        $globasset->setTargetPath('{testvar}_*.php');
        $globasset->setValues(array('testvar' => 'works'));

        foreach ($globasset as $asset) {
            $target = VarUtils::resolve($asset->getTargetPath(), $asset->getVars(), $asset->getValues());
            $this->assertStringContainsString('works', $target);
        }
    }
}
