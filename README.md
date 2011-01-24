Assetic is an asset management framework for PHP.

    $js = new AssetCollection(array(
        new GlobAsset('/path/to/js/*'),
        new FileAsset('/path/to/another.js'),
    ));

    // the code is merged when the asset is dumped
    echo $js->dump();

Filters
-------

Filters can be applied to manipulate assets.

    $css = new AssetCollection(array(
        new FileAsset('/path/to/src/styles.less', 'css/compiled.css', array(new LessFilter()),
        new GlobAsset('/path/to/css/*'),
    ), array(
        new Yui\CssCompressorFilter('/path/to/yuicompressor.jar'),
    ));

    // this will echo CSS compiled by LESS and compressed by YUI
    echo $css->dump();

The core provides the following filters in the `Assetic\Filter` namespace:

 * `CssRewriteFilter`: fixes relative URLs in CSS assets when moving to a new URL
 * `GoogleClosure\CompilerApiFilter`: compiles Javascript using the Google Closure Compiler API
 * `GoogleClosure\CompilerJarFilter`: compiles Javascript using the Google Closure Compiler JAR
 * `LessFilter`: parses LESS into CSS
 * `Sass\SassFilter`: parses SASS into CSS
 * `Sass\ScssFilter`: parses SCSS into CSS
 * `Yui\CssCompressorFilter`: compresses CSS using the YUI compressor
 * `Yui\JsCompressorFilter`: compresses Javascript using the YUI compressor

Asset Manager
-------------

An asset manager is provided for organizing assets.

    $am = new AssetManager();
    $am->set('jquery', new FileAsset('/path/to/jquery.js'));
    $am->set('base_css', new GlobAsset('/path/to/css/*'));

The asset manager can also be used to reference assets to avoid duplication.

    $am->set('my_plugin', new AssetCollection(array(
        new AssetReference($am, 'jquery'),
        new FileAsset('/path/to/jquery.plugin.js'),
    )));

Asset Factory
-------------

If you'd rather not create all these objects by hand, you can use the asset
factory, which will do most of the work for you.

    $factory = new AssetFactory('/path/to/web', $am, $fm);
    $asset = $factory->createAsset(array(
        '@reset',         // load the asset manager's "reset" asset
        'css/src/*.scss', // load everything in the core directory
        'css/foo.scss',   // load a single file
    ), array(
        'scss',           // filter through the filter manager's "scss" filter
        '?yui_css',       // use the filter manager's "yui_css" filter, if available
    ), 'css');

Caching
-------

A simple caching mechanism is provided to avoid unnecessary work.

    $yui = new Yui\JsCompressorFilter('/path/to/yuicompressor.jar');
    $js = new AssetCache(
        new FileAsset('/path/to/some.js', 'js/some.js', array($yui)),
        new FilesystemCache('/path/to/cache')
    );

    // the YUI compressor will only run on the first call
    $js->dump();
    $js->dump();
    $js->dump();

---

Assetic is based on the Python [webassets][1] library (available on
[GitHub][2]).

[1]: http://elsdoerfer.name/docs/webassets
[2]: https://github.com/miracle2k/webassets
