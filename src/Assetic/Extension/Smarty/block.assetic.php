<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------
 * Type:       block
 * Name:       assetic
 * Purpose:    smarty plugin for Assetic
 * Author:     Pierre-Jean Parra
 * Version:    1.0
 *
 * ------------------------------------------------------------
 */
use Assetic\AssetManager;
use Assetic\FilterManager;
use Assetic\Filter;
use Assetic\Factory\AssetFactory;
use Assetic\AssetWriter;
use Assetic\Asset\AssetCache;
use Assetic\Cache\FilesystemCache;

spl_autoload_register(function($class)
{
    if (0 === strpos($class, 'Assetic\\')) {
        $file = __DIR__ . '/../../../' . str_replace('\\', '/', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
			
            return true;
        }
    }
});

if (isset($_SERVER['LESSPHP'])) {
    require_once $_SERVER['LESSPHP'];
}

function smarty_block_assetic($params, $content, $template, &$repeat)
{
    // In debug mode, we have to be able to loop a certain number of times, so we use a static counter
    static $count;
    static $assetsUrls;

    // Opening tag (first call only)
    if ($repeat) {
        // Read config config file
        $config = json_decode(file_get_contents($params['config_path'] . '/config.json'));
        
        // Read bundles and dependencies config files
        $bundles = json_decode(file_get_contents($params['config_path'] . '/bundles.json'));
        $dependencies = json_decode(file_get_contents($params['config_path'] . '/dependencies.json'));
        
        $am = new AssetManager();
        
        $fm = new FilterManager();
        $fm->set('yui_js', new Filter\Yui\JsCompressorFilter($config->yuicompressor_path, $config->java_path));
        $fm->set('yui_css', new Filter\Yui\CssCompressorFilter($config->yuicompressor_path, $config->java_path));
        $fm->set('less', new Filter\LessphpFilter());
        
        // Factory setup
        $factory = new AssetFactory($config->document_root);
        $factory->setAssetManager($am);
        $factory->setFilterManager($fm);
        $factory->setDefaultOutput('assetic/*.'.$params['output']);
        
        if (isset($params['filters'])) {
            $filters = explode(',', $params['filters']);
        }
        else {
            $filters = array();
        }
        
        // Prepare the assets writer
        $writer = new AssetWriter($params['build_path']);
        
        // If a bundle name is provided
        if (isset($params['bundle'])) {
            $asset = $factory->createAsset(
                $bundles->$params['output']->$params['bundle'],
                $filters,
                array($params['debug'])
            );
            
            $cache = new AssetCache(
                $asset,
                new FilesystemCache($params['build_path'])
            );
            
            $writer->writeAsset($cache);
        }
        // If individual assets are provided
        elseif (isset($params['assets'])) {
            $assets = array();
            // Include only the references first
            foreach (explode(',', $params['assets']) as $a) {
                // If the asset is found in the dependencies file, let's create it
                // If it is not found in the assets but is needed by another asset and found in the references, don't worry, it will be automatically created
                if (isset($dependencies->$params['output']->assets->$a)) {
                    // Create the reference assets if they don't exist
                    foreach ($dependencies->$params['output']->assets->$a as $ref) {
                        try {
                            $am->get($ref);
                        }
                        catch (InvalidArgumentException $e) {
                            $assetTmp = $factory->createAsset(
                                $dependencies->$params['output']->references->$ref
                            );
                            $am->set($ref, $assetTmp);
                            $assets[] = '@'.$ref;
                        }
                    }
                }
            }
            
            // Now, include assets
            foreach (explode(',', $params['assets']) as $a) {
                // Add the asset to the list if not already present, as a reference or as a simple asset
                $ref = null;
                foreach ($dependencies->$params['output']->references as $name => $file) {
                    if ($file == $a) {
                        $ref = $name;
                        break;
                    }
                }
                
                if (array_search($a, $assets) === FALSE && ($ref === null || array_search('@' . $ref, $assets) === FALSE)) {
                    $assets[] = $a;
                }
            }

            // Create the asset
            $asset = $factory->createAsset(
                $assets,
                $filters,
                array($params['debug'])
            );
            
            $cache = new AssetCache(
                $asset,
                new FilesystemCache($params['build_path'])
            );
            
            $writer->writeAsset($cache);
        }

        // If debug mode is active, we want to include assets separately
        if ($params['debug']) {
            $assetsUrls = array();
            foreach ($asset as $a) {
                $cache = new AssetCache(
                    $a,
                    new FilesystemCache($params['build_path'])
                );
                $writer->writeAsset($cache);
                $assetsUrls[] = $a->getTargetPath();
            }
            // It's easier to fetch the array backwards, so we reverse it to insert assets in the right order
            $assetsUrls = array_reverse($assetsUrls);
            
            $count = count($assetsUrls);
            
            $template->assign($params['asset_url'], $params['build_path'].'/'.$assetsUrls[$count-1]);
        }
        // Production mode, include an all-in-one asset
        else {
            $template->assign($params['asset_url'], $params['build_path'].'/'.$asset->getTargetPath());
        }
    }
    // Closing tag
    else {
        if (isset($content)) {
            // If debug mode is active, we want to include assets separately
            if ($params['debug']) {
                $count--;
                $template->assign($params['asset_url'], $params['build_path'].'/'.$assetsUrls[$count-1]);
                $repeat = $count > 0;
            }

            return $content;
        }
    }

}
