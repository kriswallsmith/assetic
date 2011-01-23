Assetic is an asset management framework for PHP.

    $js = new AssetCollection(array(
        new GlobAsset('/path/to/js/*'),
        new FileAsset('/path/to/another.js'),
    ));
    $js->load();

    // the merged code is returned when the asset is dumped
    echo $js->dump();

Filters
-------

Filters can be applied to manipulate assets.

    $css = new AssetCollection(array(
        new FileAsset('/path/to/src/styles.less', 'css/compiled.css', array(new LessFilter()),
        new GlobAsset('/path/to/css/*'),
    ), array(
        new YuiCompressorCssFilter('/path/to/yuicompressor.jar'),
    ));
    $css->load();

    // this will echo CSS compiled by LESS and compressed by YUI
    echo $css->dump();

The core provides the following filters in the `Assetic\Filter` namespace:

 * `CssRewriteFilter`: fixes relative URLs in CSS assets when moving to a new URL
 * `GoogleClosure\CompilerApiFilter`: compiles Javascript using the Google Closure Compiler API
 * `GoogleClosure\CompilerJarFilter`: compiles Javascript using the Google Closure Compiler JAR
 * `LessFilter`: parses LESS into CSS
 * `Sass\SassFilter`: parses SASS into CSS
 * `Sass\ScssFilter`: parses SCSS into CSS
 * `Yui\YuiCompressorCssFilter`: compresses CSS using the YUI compressor
 * `Yui\YuiCompressorJsFilter`: compresses Javascript using the YUI compressor

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

Caching
-------

A simple caching mechanism is provided to avoid unnecessary work.

    $yui = new YuiCompressorJsFilter('/path/to/yuicompressor.jar');
    $js = new AssetCache(
        new FileAsset('/path/to/some.js', 'js/some.js', array($yui)),
        new FilesystemCache('/path/to/cache')
    );

    // filtering will only happen the first time each method is called
    $js->load();
    $js->load();
    $js->dump();
    $js->dump();

---

Assetic is based on the Python [webassets][1] library (available on
[GitHub][2]).

[1]: http://elsdoerfer.name/docs/webassets
[2]: https://github.com/miracle2k/webassets
