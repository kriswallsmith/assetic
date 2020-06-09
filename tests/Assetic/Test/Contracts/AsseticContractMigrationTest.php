<?php namespace Assetic\Test\Contracts;

use PHPUnit\Framework\TestCase;

class AsseticContractMigrationTest extends TestCase
{
    public function testAssetInterface()
    {
        $this->assertTrue(interface_exists(\Assetic\Asset\AssetInterface::class));
    }

    public function testAssetCollectionInterface()
    {
        $this->assertTrue(interface_exists(\Assetic\Asset\AssetCollectionInterface::class));
    }

    public function testCacheInterface()
    {
        $this->assertTrue(interface_exists(\Assetic\Cache\CacheInterface::class));
    }

    public function testException()
    {
        $this->assertTrue(interface_exists(\Assetic\Exception\Exception::class));
    }

    public function testFormulaLoaderInterface()
    {
        $this->assertTrue(interface_exists(\Assetic\Factory\Loader\FormulaLoaderInterface::class));
    }

    public function testIteratorResourceInterface()
    {
        $this->assertTrue(interface_exists(\Assetic\Resource\IteratorResourceInterface::class));
    }

    public function testResourceInterface()
    {
        $this->assertTrue(interface_exists(\Assetic\Resource\ResourceInterface::class));
    }

    public function testWorkerInterface()
    {
        $this->assertTrue(interface_exists(\Assetic\Worker\WorkerInterface::class));
    }

    public function testDependencyExtractorInterface()
    {
        $this->assertTrue(interface_exists(\Assetic\Filter\DependencyExtractorInterface::class));
    }

    public function testFilterInterface()
    {
        $this->assertTrue(interface_exists(\Assetic\Filter\FilterInterface::class));
    }

    public function testHashableInterface()
    {
        $this->assertTrue(interface_exists(\Assetic\Filter\HashableInterface::class));
    }

    public function testValueSupplierInterface()
    {
        $this->assertTrue(interface_exists(\Assetic\ValueSupplierInterface::class));
    }
}
