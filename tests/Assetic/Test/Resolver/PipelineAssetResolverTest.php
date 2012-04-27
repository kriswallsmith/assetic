<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Resolver;

use Assetic\Resolver\PipelineAssetResolver;

class PipelineAssetResolverTest extends \PHPUnit_Framework_TestCase
{
    private $path;
    private $locator;

    protected function setUp()
    {
        $this->path = realpath(__DIR__.'/../Fixture/pipeline');

        $this->locator = new PipelineAssetResolver(array(
            $this->path.'/track3',
            $this->path.'/track2',
            $this->path.'/track1',
        ));
    }

    public function testresolveSingleAsset()
    {
        $this->assertNotNull($asset = $this->locator->resolve(
            'simple_script', array('vars' => array(), 'output' => '*.js', 'root' => array())
        ));
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $asset);
        $this->assertSame($this->path.'/track1/js', $asset->getSourceRoot());
        $this->assertSame('simple_script.js', $asset->getSourcePath());

        $this->assertNotNull($asset = $this->locator->resolve(
            'subdir/subscript.js', array('vars' => array(), 'output' => '*.js', 'root' => array())
        ));
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $asset);
        $this->assertSame($this->path.'/track2/js/subdir', $asset->getSourceRoot());
        $this->assertSame('subscript.js', $asset->getSourcePath());

        $this->assertNotNull($asset = $this->locator->resolve(
            'style', array('vars' => array(), 'output' => '*.css', 'root' => array())
        ));
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $asset);
        $this->assertSame($this->path.'/track3/css', $asset->getSourceRoot());
        $this->assertSame('style.css', $asset->getSourcePath());
    }

    public function testresolveIndexAsset()
    {
        $this->assertNotNull($asset = $this->locator->resolve(
            'library', array('vars' => array(), 'output' => '*.js', 'root' => array())
        ));
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $asset);
        $this->assertSame($this->path.'/track3/js/library', $asset->getSourceRoot());
        $this->assertSame('index.js', $asset->getSourcePath());

        $this->assertNotNull($asset = $this->locator->resolve(
            'library/sublib', array('vars' => array(), 'output' => '*.css', 'root' => array())
        ));
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $asset);
        $this->assertSame($this->path.'/track2/css/library/sublib', $asset->getSourceRoot());
        $this->assertSame('index.css', $asset->getSourcePath());
    }

    public function testresolveDirectoryAssets()
    {
        $this->assertNotNull($asset = $this->locator->resolve(
            'unindexed_library', array('vars' => array(), 'output' => '*.js', 'root' => array())
        ));
        $this->assertInstanceOf('Assetic\\Asset\\AssetCollection', $asset);

        $assets = $asset->all();
        $this->assertSame(2, count($assets));

        $asset = $assets[0];
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $asset);
        $this->assertSame($this->path.'/track3/js/unindexed_library', $asset->getSourceRoot());
        $this->assertSame('file1.js', $asset->getSourcePath());

        $asset = $assets[1];
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $asset);
        $this->assertSame($this->path.'/track3/js/unindexed_library', $asset->getSourceRoot());
        $this->assertSame('file2.js', $asset->getSourcePath());
    }

    public function testresolveTreeAssets()
    {
        $this->assertNotNull($asset = $this->locator->resolve(
            'unindexed_tree', array('vars' => array(), 'output' => '*.css', 'root' => array(), 'type' => 'tree/css')
        ));
        $this->assertInstanceOf('Assetic\\Asset\\AssetCollection', $asset);

        $assets = $asset->all();
        $this->assertSame(3, count($assets));

        $asset = $assets[0];
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $asset);
        $this->assertSame($this->path.'/track1/css/unindexed_tree', $asset->getSourceRoot());
        $this->assertSame('global.css.scss', $asset->getSourcePath());

        $asset = $assets[1];
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $asset);
        $this->assertSame($this->path.'/track1/css/unindexed_tree/menu', $asset->getSourceRoot());
        $this->assertSame('main.css', $asset->getSourcePath());

        $asset = $assets[2];
        $this->assertInstanceOf('Assetic\\Asset\\FileAsset', $asset);
        $this->assertSame($this->path.'/track1/css/unindexed_tree/menu/user', $asset->getSourceRoot());
        $this->assertSame('bar.css.less', $asset->getSourcePath());
    }
}
