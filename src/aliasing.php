<?php

/**
 * Class aliasing allowing for easier migration from v1 to v2.
 *
 * @author Jack Wilkinson <me@jackwilky.com>
 */

// Only run the aliasing once - Fixes preloading support on PHP 7.4+
if (interface_exists(Assetic\Asset\AssetInterface::class, false)) {
    return;
}

class_alias(
    Assetic\Contracts\Asset\AssetInterface::class,
    Assetic\Asset\AssetInterface::class
);

class_alias(
    Assetic\Contracts\Asset\AssetCollectionInterface::class,
    Assetic\Asset\AssetCollectionInterface::class
);

class_alias(
    Assetic\Contracts\Cache\CacheInterface::class,
    Assetic\Cache\CacheInterface::class
);

class_alias(
    Assetic\Contracts\Exception\Exception::class,
    Assetic\Exception\Exception::class
);

class_alias(
    Assetic\Contracts\Factory\Loader\FormulaLoaderInterface::class,
    Assetic\Factory\Loader\FormulaLoaderInterface::class
);

class_alias(
    Assetic\Contracts\Factory\Resource\IteratorResourceInterface::class,
    Assetic\Resource\IteratorResourceInterface::class
);

class_alias(
    Assetic\Contracts\Factory\Resource\ResourceInterface::class,
    Assetic\Resource\ResourceInterface::class
);

class_alias(
    Assetic\Contracts\Factory\Worker\WorkerInterface::class,
    Assetic\Worker\WorkerInterface::class
);

class_alias(
    Assetic\Contracts\Filter\DependencyExtractorInterface::class,
    Assetic\Filter\DependencyExtractorInterface::class
);

class_alias(
    Assetic\Contracts\Filter\FilterInterface::class,
    Assetic\Filter\FilterInterface::class
);

class_alias(
    Assetic\Contracts\Filter\HashableInterface::class,
    Assetic\Filter\HashableInterface::class
);

class_alias(
    Assetic\Contracts\ValueSupplierInterface::class,
    Assetic\ValueSupplierInterface::class
);
