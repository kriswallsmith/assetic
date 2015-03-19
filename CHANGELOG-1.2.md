1.2.0 (2014-10-14)
------------------

### New features

* Added the autoprefixer filter
* Added --no-header option for Coffeescript
* Implemented the extraction of dependencies for the compass filter
* Allow custom functions to be registered on the Lessphp filter
* Added MinifyCssCompressor filter based on `mrclay/minify`
* Added `setVariables` in the ScssPhpFilter
* Improved the support of the compress options for UglifyJS2
* Added CssCacheBustingFilter to apply cache busting on URLs in the CSS
* Added support for `--relative-assets` option for the compass filter

### Bug fixes

* Allow functions.php to be included many times
* Updated the ScssPhpFilter for upstream class renaming

1.2.0-alpha1 (2014-07-08)
-------------------------

### BC breaks

* Added `AssetFactory` instance as second argument for `WorkerInterface::process()`
* Removed `LazyAssetManager` from `CacheBustingWorker` constructor
* A new `getSourceDirectory()` method was added on the AssetInterface
* Removed limit and count arguments from CssUtils functions
* Removed the deprecated `PathUtils` class

### New features

* added `CssUtils::filterCommentless()`
* Added `DependencyExtractorInterface` for filters to report other files they import
* Added the support of nib in the stylus filter
* Added `registerFunction` and `setFormatter` to the scssphp filter
* Added the support of flag file for the ClosureCompilerJarFilter
* Added the JsSqueeze filter
* Added support of the define option for uglifyjs (1 & 2) filters
* Added logging for Twig errors in the extractor

### Bug fixes

* Fixed the detection of protocol-relative CSS imports
* Updated AssetCollection to not add several time the same variable in path
* Fixed the merging of the environment variables to keep the configuration of the NODE_PATH working
* Fixed the support of ``{% embed %}`` in the Twig extractor
* Fixed the support of asset variables in GlobAsset
