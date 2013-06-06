1.2-dev
-------

 * [BC BREAK] Added `AssetFactory` instance as second argument for
   `WorkerInterface::process()`
 * [BC BREAK] Removed `LazyAssetManager` from `CacheBustingWorker` constructor
 * A new `getSourceDirectory()` method was added on the AssetInterface
 * [BC BREAK] Changed the DependencyExtractor interface to take the Asset, not
   only its contents and source directory.
