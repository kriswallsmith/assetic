1.2-dev
-------

 * [BC BREAK] Added `AssetFactory` instance as second argument for
   `WorkerInterface::process()`
 * [BC BREAK] Removed `LazyAssetManager` from `CacheBustingWorker` constructor
 * A new `getSourceDirectory()` method was added on the AssetInterface
 * added CssUtils::filterCommentless()
 * [BC BREAK] Removed limit and count arguments from CssUtils functions
