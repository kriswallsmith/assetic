# Assetic
[![Build Status](https://travis-ci.org/assetic-php/assetic.svg?branch=master)](https://travis-ci.org/assetic-php/assetic)
[![Coverage Status](https://coveralls.io/repos/github/assetic-php/assetic/badge.svg?branch=master)](https://coveralls.io/github/assetic-php/assetic?branch=master)
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/assetic-php/assetic.svg)](http://isitmaintained.com/project/assetic-php/assetic "Average time to resolve an issue")
[![Percentage of issues still open](https://isitmaintained.com/badge/open/assetic-php/assetic.svg)](https://isitmaintained.com/project/assetic-php/assetic "Percentage of issues still open")

Assetic is an asset management framework for PHP maintained by the [OctoberCMS team](https://github.com/octobercms).

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
use Assetic\Filter\UglifyCssFilter;

$css = new AssetCollection(array(
    new FileAsset('/path/to/src/styles.less', array(new LessFilter())),
    new GlobAsset('/path/to/css/*'),
), array(
    new UglifyCssFilter('/path/to/uglifycss'),
));

// this will echo CSS compiled by LESS and compressed by uglifycss
echo $css->dump();
```

The filters applied to the collection will cascade to each asset leaf if you
iterate over it.

``` php
<?php

foreach ($css as $leaf) {
    // each leaf is compressed by uglifycss
    echo $leaf->dump();
}
```

The core provides the following filters in the `Assetic\Filter` namespace:

 * `CoffeeScriptFilter`: compiles CoffeeScript into Javascript
 * `CssImportFilter`: inlines imported stylesheets
 * `CssMinFilter`: minifies CSS
 * `CssRewriteFilter`: fixes relative URLs in CSS assets when moving to a new URL
 * `GoogleClosure\CompilerApiFilter`: compiles Javascript using the Google Closure Compiler API
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
 * `ScssFilter`: parses SCSS into CSS
 * `SeparatorFilter`: inserts a separator between assets to prevent merge failures
 * `StylusFilter`: parses STYL into CSS
 * `TypeScriptFilter`: parses TypeScript into Javascript
 * `UglifyCssFilter`: minifies CSS
 * `UglifyJs2Filter`: minifies Javascript
 * `UglifyJsFilter`: minifies Javascript

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
use Assetic\Filter\ScssFilter;
use Assetic\Filter\CssMinFilter;

$fm = new FilterManager();
$fm->set('sass', new ScssFilter('/path/to/parser/scss'));
$fm->set('cssmin', new CssMinFilter());
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
    '?cssmin',        // don't use this filter in debug mode
));

echo $css->dump();
```

The `AssetFactory` is constructed with a root directory which is used as the base directory for relative asset paths.

Prefixing a filter name with a question mark, as `cssmin` is here, will cause
that filter to be omitted when the factory is in debug mode.

You can also register [Workers](src/Assetic/Contracts/Factory/Worker/WorkerInterface.php) on the factory and all assets created
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
use Assetic\Filter\JsMinFilter;

$jsMin = new JsMinFilter();
$js = new AssetCache(
    new FileAsset('/path/to/some.js', array($jsMin)),
    new FilesystemCache('/path/to/cache')
);

// the JsMin compressor will only run on the first call
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

---

Assetic is based on the Python [webassets][1] library (available on
[GitHub][2]).

[1]: http://elsdoerfer.name/docs/webassets
[2]: https://github.com/miracle2k/webassets
[3]: http://twig.sensiolabs.org
