Assetic is an asset management framework for PHP.

    $js = new AssetCollection(array(
        new GlobAsset('/path/to/js/*'),
        new FileAsset('/path/to/another.js'),
    ));
    $js->load();

    // the merged code is returned when the asset is dumped
    echo $js->dump();

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

An asset manager is provided for organizing assets.

    $am = new AssetManager();
    $am->set('jquery', new FileAsset('/path/to/jquery.js'));
    $am->set('base_css', new GlobAsset('/path/to/css/*'));

The asset manager can also be used to reference assets to avoid duplication.

    $am->set('my_plugin', new AssetCollection(array(
        new AssetReference($am, 'jquery'),
        new FileAsset('/path/to/jquery.plugin.js'),
    )));

---

Assetic is based on the Python [webassets][1] library (available on
[GitHub][2]).

[1]: http://elsdoerfer.name/docs/webassets
[2]: https://github.com/miracle2k/webassets
