Assetic is an asset management framework for PHP.

    $js = new AssetCollection(array(
        new GlobAsset('/path/to/js/*'),
        new FileAsset('/path/to/another.js'),
    ));

    // the code is merged when the asset is dumped
    echo $js->dump();

Alternatively, you can iterate over the collection and work with each asset
individually.

    // each asset "leaf" is dumped
    foreach ($js as $leaf) {
        echo $leaf->dump();
    }

Filters
-------

Filters can be applied to manipulate assets.

    $css = new AssetCollection(array(
        new FileAsset('/path/to/src/styles.less', array(new LessFilter())),
        new GlobAsset('/path/to/css/*'),
    ), array(
        new Yui\CssCompressorFilter('/path/to/yuicompressor.jar'),
    ));

    // this will echo CSS compiled by LESS and compressed by YUI
    echo $css->dump();

The filters applied to the collection will cascade to each asset leaf if you
iterate over it.

    foreach ($css as $leaf) {
        // each leaf is compressed by YUI
        echo $leaf->dump();
    }

The core provides the following filters in the `Assetic\Filter` namespace:

 * `CoffeeScriptFilter`: compiles CoffeeScript into Javascript
 * `CssRewriteFilter`: fixes relative URLs in CSS assets when moving to a new URL
 * `GoogleClosure\CompilerApiFilter`: compiles Javascript using the Google Closure Compiler API
 * `GoogleClosure\CompilerJarFilter`: compiles Javascript using the Google Closure Compiler JAR
 * `LessFilter`: parses LESS into CSS
 * `Sass\SassFilter`: parses SASS into CSS
 * `Sass\ScssFilter`: parses SCSS into CSS
 * `SprocketsFilter`: Sprockets Javascript dependency management
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

    $factory = new AssetFactory('/path/to/web');
    $factory->setAssetManager($am);
    $factory->setFilterManager($fm);
    $factory->setDebug(true);

    $css = $factory->createAsset(array(
        '@reset',         // load the asset manager's "reset" asset
        'css/src/*.scss', // load everything in the core directory
    ), array(
        'scss',           // filter through the filter manager's "scss" filter
        '?yui_css',       // don't use this filter in debug mode
    ));

    echo $css->dump();

Prefixing a filter name with a question mark, as `yui_css` is here, will cause
that filter to be omitted when the factory is in debug mode.

Caching
-------

A simple caching mechanism is provided to avoid unnecessary work.

    $yui = new Yui\JsCompressorFilter('/path/to/yuicompressor.jar');
    $js = new AssetCache(
        new FileAsset('/path/to/some.js', array($yui)),
        new FilesystemCache('/path/to/cache')
    );

    // the YUI compressor will only run on the first call
    $js->dump();
    $js->dump();
    $js->dump();

Static Assets
-------------

Alternatively you can just write filtered assets to your web directory and be
done with it.

    $writer = new AssetWriter('/path/to/web');
    $writer->writeManagerAssets($am);

Twig
----

To use the Assetic [Twig][3] extension you must register it to your Twig
environment:

    $twig->addExtension(new AsseticExtension($factory, $debug));

Once in place, the extension exposes an `assetic` tag with a syntax similar
to what the asset factory uses:

    {% assets '/path/to/sass/main.sass' filter='sass,?yui_css' output='css' %}
        <link href="{{ asset_url }}" type="text/css" rel="stylesheet" />
    {% endassets %}

This example will render one `link` element on the page that includes a URL
where the filtered asset can be found.

When the extension is in debug mode, this same tag will render multiple `link`
elements, one for each asset referenced by the `css/src/*.sass` glob. The
specified filters will still be applied, unless they are marked as optional
using the `?` prefix.

This behavior can also be triggered by setting a `debug` attribute on the tag:

    {% assets 'css/*' debug=true %} ... {% endassets %}

These assets need to be written to the web directory so these URLs don't
return 404 errors.

    $am = new LazyAssetManager($factory);

    // loop through all your templates
    $loader = new Twig\FormulaLoader($twig);
    foreach ($templates as $template) {
        $am->addFormulae($loader->load($template));
    }

    $writer = new AssetWriter('/path/to/web');
    $writer->writeManagerAssets($am);

---

Assetic is based on the Python [webassets][1] library (available on
[GitHub][2]).

[1]: http://elsdoerfer.name/docs/webassets
[2]: https://github.com/miracle2k/webassets
[3]: http://www.twig-project.org
