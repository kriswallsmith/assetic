# Assetic [![Build Status](https://travis-ci.org/kriswallsmith/assetic.png?branch=master)](https://travis-ci.org/kriswallsmith/assetic) ![project status](http://stillmaintained.com/kriswallsmith/assetic.png) #

Assetic is an asset management framework for PHP.

``` php
<?php

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;

$js = new AssetCollection(array(
    new GlobAsset('/path/to/js/*'),
    new FileAsset('/path/to/another.js'),
));

// the code is merged when the asset is dumped
echo $js->dump();
```

Assets
------

An Assetic asset is something with filterable content that can be loaded and
dumped. An asset also includes metadata, some of which can be manipulated and
some of which is immutable.

| **Property** | **Accessor**    | **Mutator**   |
|--------------|-----------------|---------------|
| content      | getContent      | setContent    |
| mtime        | getLastModified | n/a           |
| source root  | getSourceRoot   | n/a           |
| source path  | getSourcePath   | n/a           |
| target path  | getTargetPath   | setTargetPath |

The "target path" property denotes where an asset (or an collection of assets) should be dumped.

Filters
-------

Filters can be applied to manipulate assets.

``` php
<?php

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Filter\LessFilter;
use Assetic\Filter\Yui;

$css = new AssetCollection(array(
    new FileAsset('/path/to/src/styles.less', array(new LessFilter())),
    new GlobAsset('/path/to/css/*'),
), array(
    new Yui\CssCompressorFilter('/path/to/yuicompressor.jar'),
));

// this will echo CSS compiled by LESS and compressed by YUI
echo $css->dump();
```

The filters applied to the collection will cascade to each asset leaf if you
iterate over it.

``` php
<?php

foreach ($css as $leaf) {
    // each leaf is compressed by YUI
    echo $leaf->dump();
}
```

The core provides the following filters in the `Assetic\Filter` namespace:

 * `AutoprefixerFilter`: Parse and update vendor-specific properties using autoprefixer
 * `CoffeeScriptFilter`: compiles CoffeeScript into Javascript
 * `CompassFilter`: Compass CSS authoring framework
 * `CssEmbedFilter`: embeds image data in your stylesheets
 * `CssImportFilter`: inlines imported stylesheets
 * `CssMinFilter`: minifies CSS
 * `CleanCssFilter`: minifies CSS
 * `CssRewriteFilter`: fixes relative URLs in CSS assets when moving to a new URL
 * `DartFilter`: compiles Javascript using dart2js
 * `EmberPrecompileFilter`: precompiles Handlebars templates into Javascript for use in the Ember.js framework
 * `GoogleClosure\CompilerApiFilter`: compiles Javascript using the Google Closure Compiler API
 * `GoogleClosure\CompilerJarFilter`: compiles Javascript using the Google Closure Compiler JAR
 * `GssFilter`: compliles CSS using the Google Closure Stylesheets Compiler
 * `HandlebarsFilter`: compiles Handlebars templates into Javascript
 * `JpegoptimFilter`: optimize your JPEGs
 * `JpegtranFilter`: optimize your JPEGs
 * `JSMinFilter`: minifies Javascript
 * `JSMinPlusFilter`: minifies Javascript
 * `JSqueezeFilter`: compresses Javascript
 * `LessFilter`: parses LESS into CSS (using less.js with node.js)
 * `LessphpFilter`: parses LESS into CSS (using lessphp)
 * `OptiPngFilter`: optimize your PNGs
 * `PackagerFilter`: parses Javascript for packager tags
 * `PackerFilter`: compresses Javascript using Dean Edwards's Packer
 * `PhpCssEmbedFilter`: embeds image data in your stylesheet
 * `PngoutFilter`: optimize your PNGs
 * `ReactJsxFilter`: compiles React JSX into JavaScript
 * `Sass\SassFilter`: parses SASS into CSS
 * `Sass\ScssFilter`: parses SCSS into CSS
 * `SassphpFilter`: parses Sass into CSS using the sassphp bindings for Libsass
 * `ScssphpFilter`: parses SCSS using scssphp
 * `SeparatorFilter`: inserts a separator between assets to prevent merge failures
 * `SprocketsFilter`: Sprockets Javascript dependency management
 * `StylusFilter`: parses STYL into CSS
 * `TypeScriptFilter`: parses TypeScript into Javascript
 * `UglifyCssFilter`: minifies CSS
 * `UglifyJs2Filter`: minifies Javascript
 * `UglifyJsFilter`: minifies Javascript
 * `Yui\CssCompressorFilter`: compresses CSS using the YUI compressor
 * `Yui\JsCompressorFilter`: compresses Javascript using the YUI compressor

Asset Manager
-------------

An asset manager is provided for organizing assets.

``` php
<?php

use Assetic\AssetManager;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;

$am = new AssetManager();
$am->set('jquery', new FileAsset('/path/to/jquery.js'));
$am->set('base_css', new GlobAsset('/path/to/css/*'));
```

The asset manager can also be used to reference assets to avoid duplication.

``` php
<?php

use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetReference;
use Assetic\Asset\FileAsset;

$am->set('my_plugin', new AssetCollection(array(
    new AssetReference($am, 'jquery'),
    new FileAsset('/path/to/jquery.plugin.js'),
)));
```

Filter Manager
--------------

A filter manager is also provided for organizing filters.

``` php
<?php

use Assetic\FilterManager;
use Assetic\Filter\Sass\SassFilter;
use Assetic\Filter\Yui;

$fm = new FilterManager();
$fm->set('sass', new SassFilter('/path/to/parser/sass'));
$fm->set('yui_css', new Yui\CssCompressorFilter('/path/to/yuicompressor.jar'));
```

Asset Factory
-------------

If you'd rather not create all these objects by hand, you can use the asset
factory, which will do most of the work for you.

``` php
<?php

use Assetic\Factory\AssetFactory;

$factory = new AssetFactory('/path/to/asset/directory/');
$factory->setAssetManager($am);
$factory->setFilterManager($fm);
$factory->setDebug(true);

$css = $factory->createAsset(array(
    '@reset',         // load the asset manager's "reset" asset
    'css/src/*.scss', // load every scss files from "/path/to/asset/directory/css/src/"
), array(
    'scss',           // filter through the filter manager's "scss" filter
    '?yui_css',       // don't use this filter in debug mode
));

echo $css->dump();
```

The `AssetFactory` is constructed with a root directory which is used as the base directory for relative asset paths.

Prefixing a filter name with a question mark, as `yui_css` is here, will cause
that filter to be omitted when the factory is in debug mode.

You can also register [Workers](src/Assetic/Factory/Worker/WorkerInterface.php) on the factory and all assets created
by it will be passed to the worker's `process()` method before being returned. See _Cache Busting_ below for an example.

Dumping Assets to static files
------------------------------

You can dump all the assets an AssetManager holds to files in a directory. This will probably be below your webserver's document root
so the files can be served statically.

``` php
<?php

use Assetic\AssetWriter;

$writer = new AssetWriter('/path/to/web');
$writer->writeManagerAssets($am);
```

This will make use of the assets' target path.

Cache Busting
-------------

If you serve your assets from static files as just described, you can use the CacheBustingWorker to rewrite the target
paths for assets. It will insert an identifier before the filename extension that is unique for a particular version
of the asset.

This identifier is based on the modification time of the asset and will also take depended-on assets into
consideration if the applied filters support it.

``` php
<?php

use Assetic\Factory\AssetFactory;
use Assetic\Factory\Worker\CacheBustingWorker;

$factory = new AssetFactory('/path/to/asset/directory/');
$factory->setAssetManager($am);
$factory->setFilterManager($fm);
$factory->setDebug(true);
$factory->addWorker(new CacheBustingWorker());

$css = $factory->createAsset(array(
    '@reset',         // load the asset manager's "reset" asset
    'css/src/*.scss', // load every scss files from "/path/to/asset/directory/css/src/"
), array(
    'scss',           // filter through the filter manager's "scss" filter
    '?yui_css',       // don't use this filter in debug mode
));

echo $css->dump();
```

Internal caching
-------

A simple caching mechanism is provided to avoid unnecessary work.

``` php
<?php

use Assetic\Asset\AssetCache;
use Assetic\Asset\FileAsset;
use Assetic\Cache\FilesystemCache;
use Assetic\Filter\Yui;

$yui = new Yui\JsCompressorFilter('/path/to/yuicompressor.jar');
$js = new AssetCache(
    new FileAsset('/path/to/some.js', array($yui)),
    new FilesystemCache('/path/to/cache')
);

// the YUI compressor will only run on the first call
$js->dump();
$js->dump();
$js->dump();
```

Twig
----

To use the Assetic [Twig][3] extension you must register it to your Twig
environment:

``` php
<?php

$twig->addExtension(new AsseticExtension($factory));
```

Once in place, the extension exposes a stylesheets and a javascripts tag with a syntax similar
to what the asset factory uses:

``` html+jinja
{% stylesheets '/path/to/sass/main.sass' filter='sass,?yui_css' output='css/all.css' %}
    <link href="{{ asset_url }}" type="text/css" rel="stylesheet" />
{% endstylesheets %}
```

This example will render one `link` element on the page that includes a URL
where the filtered asset can be found.

When the extension is in debug mode, this same tag will render multiple `link`
elements, one for each asset referenced by the `css/src/*.sass` glob. The
specified filters will still be applied, unless they are marked as optional
using the `?` prefix.

This behavior can also be triggered by setting a `debug` attribute on the tag:

``` html+jinja
{% stylesheets 'css/*' debug=true %} ... {% stylesheets %}
```

These assets need to be written to the web directory so these URLs don't
return 404 errors.

``` php
<?php

use Assetic\AssetWriter;
use Assetic\Extension\Twig\TwigFormulaLoader;
use Assetic\Extension\Twig\TwigResource;
use Assetic\Factory\LazyAssetManager;

$am = new LazyAssetManager($factory);

// enable loading assets from twig templates
$am->setLoader('twig', new TwigFormulaLoader($twig));

// loop through all your templates
foreach ($templates as $template) {
    $resource = new TwigResource($twigLoader, $template);
    $am->addResource($resource, 'twig');
}

$writer = new AssetWriter('/path/to/web');
$writer->writeManagerAssets($am);
```

Smarty
------

Assetic can also easily be integrated with Smarty, as it provides a Smarty plugin.

First, you have to add Assetic/Extension/Smarty to your Smarty plugins_dir:

``` php
$smarty = new Smarty();
// ...
$smarty->plugins_dir[] = 'path/to/Assetic/Extension/Smarty';
```

A simple usage example:

``` html
{assetic 
    assets="style/reset.css,style/common.css,style/other.css" 
    output="css" 
    config_path="config" 
    build_path="style/build" 
    debug=false 
    filters="yui_css,less" 
    asset_url=asset_url}
    <link rel="stylesheet" href="{$asset_url}">
{/assetic}
```

Here is a list of the possible parameters:

* assets: A coma-separated list of files to include in the build (can be CSS or JS files)
* bundle: A bundle name. We will talk about it later. Incompatible with the "assets" parameter, you have to choose between the two.
* output: The kind of output you want, can be "css" or "js"
* config_path: The folder where the plugin will find the configuration files (we will talk about these ones later)
* build_path: The folder where the plugin will store your all-in-one asset
* debug: If set to true, the plugin will not combine your assets to allow easier debug
* filters: A coma-separated list of filters to apply. Currently, only LESS and YuiCompressor (both CSS and JS) are supported
* asset_url: The variable name that will be used to pass the asset URL to the &lt;link&gt; tag

There are 3 external files you need to provide:

config.json:

``` json
{
    "autoload_path": "/path/to/autoload.php",
    "yuicompressor_path": "/path/to/yuicompressor.jar",
    "java_path": "/usr/bin/java,
    "document_root": "/your/document/root"
}
```

Following your preferences, you can choose one of the 2 other files:

bundles.json allows you to simply tell which files you want to include in a bundle:

``` json
{
    "css":
    {
        "main":
        [
            "css/reset.css",
            "css/common.css",
            "css/other.css"
        ]
    }
}
```

This file would allow you to use the "main" bundle name in the "bundle" parameter rather than just giving the list of file names to the "assets" parameter

dependencies.json is a bit more complicated but allows you to benefit from the AssetReference Assetic's feature. Very useful for JS assets:

``` json
{
    "js":
    {
        "references":
        {
            "awesomelib": "js/awesome.lib.js",
            "anotherlib": "js/another.lib.js",
            "somejs": "js/just.some.js.file.js"
        },
        "assets":
        {
            "js/foo.js": ["awesomelib","anotherlib"],
            "js/bar.js": ["somejs"],
            "js/no.dependencies.js": []
        }
    }
}
```

This allows you to keep track of dependencies between JS files, which can be very handy. Then, you only specify the names of your "useful" JS files in the "assets" parameter. The plugin will take care of including only the necessary dependencies for you, without any duplicates.
So if you write "js/foo.js,js/no.dependencies.js" in the assets parameter, the plugin will combine js/awesome.lib.js, js/another.lib.hs, js/foo.js and js/no.dependencies.js into the build.

---

Assetic is based on the Python [webassets][1] library (available on
[GitHub][2]).

[1]: http://elsdoerfer.name/docs/webassets
[2]: https://github.com/miracle2k/webassets
[3]: http://twig.sensiolabs.org
