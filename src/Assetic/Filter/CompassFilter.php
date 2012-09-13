<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Exception\FilterException;
use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Loads Compass files.
 *
 * @link http://compass-style.org/
 * @author Maxime Thirouin <maxime.thirouin@gmail.com>
 */
class CompassFilter implements FilterInterface
{
    private $compassPath;
    private $rubyPath;
    private $scss;

    // sass options
    private $unixNewlines;
    private $debugInfo;
    private $cacheLocation;
    private $noCache;

    // compass options
    private $force;
    private $style;
    private $quiet;
    private $boring;
    private $noLineComments;
    private $imagesDir;
    private $javascriptsDir;

    // compass configuration file options
    private $plugins = array();
    private $loadPaths = array();
    private $httpPath;
    private $httpImagesPath;
    private $httpGeneratedImagesPath;
    private $generatedImagesPath;
    private $httpJavascriptsPath;
    private $homeEnv = true;

    public function __construct($compassPath = '/usr/bin/compass', $rubyPath = null)
    {
        $this->compassPath = $compassPath;
        $this->rubyPath = $rubyPath;
        $this->cacheLocation = sys_get_temp_dir();

        if ('cli' !== php_sapi_name()) {
            $this->boring = true;
        }
    }

    public function setScss($scss)
    {
        $this->scss = $scss;
    }

    // sass options setters
    public function setUnixNewlines($unixNewlines)
    {
        $this->unixNewlines = $unixNewlines;
    }

    public function setDebugInfo($debugInfo)
    {
        $this->debugInfo = $debugInfo;
    }

    public function setCacheLocation($cacheLocation)
    {
        $this->cacheLocation = $cacheLocation;
    }

    public function setNoCache($noCache)
    {
        $this->noCache = $noCache;
    }

    // compass options setters
    public function setForce($force)
    {
        $this->force = $force;
    }

    public function setStyle($style)
    {
        $this->style = $style;
    }

    public function setQuiet($quiet)
    {
        $this->quiet = $quiet;
    }

    public function setBoring($boring)
    {
        $this->boring = $boring;
    }

    public function setNoLineComments($noLineComments)
    {
        $this->noLineComments = $noLineComments;
    }

    public function setImagesDir($imagesDir)
    {
        $this->imagesDir = $imagesDir;
    }

    public function setJavascriptsDir($javascriptsDir)
    {
        $this->javascriptsDir = $javascriptsDir;
    }

    // compass configuration file options setters
    public function setPlugins(array $plugins)
    {
        $this->plugins = $plugins;
    }

    public function addPlugin($plugin)
    {
        $this->plugins[] = $plugin;
    }

    public function setLoadPaths(array $loadPaths)
    {
        $this->loadPaths = $loadPaths;
    }

    public function addLoadPath($loadPath)
    {
        $this->loadPaths[] = $loadPath;
    }

    public function setHttpPath($httpPath)
    {
        $this->httpPath = $httpPath;
    }

    public function setHttpImagesPath($httpImagesPath)
    {
        $this->httpImagesPath = $httpImagesPath;
    }

    public function setHttpGeneratedImagesPath($httpGeneratedImagesPath)
    {
        $this->httpGeneratedImagesPath = $httpGeneratedImagesPath;
    }

    public function setGeneratedImagesPath($generatedImagesPath)
    {
        $this->generatedImagesPath = $generatedImagesPath;
    }

    public function setHttpJavascriptsPath($httpJavascriptsPath)
    {
        $this->httpJavascriptsPath = $httpJavascriptsPath;
    }

    public function setHomeEnv($homeEnv)
    {
        $this->homeEnv = $homeEnv;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();

        $loadPaths = $this->loadPaths;
        if ($root && $path) {
            $loadPaths[] = dirname($root.'/'.$path);
        }

        // compass does not seems to handle symlink, so we use realpath()
        $tempDir = realpath(sys_get_temp_dir());

        $compassProcessArgs = array(
            $this->compassPath,
            'compile',
            $tempDir,
        );
        if (null !== $this->rubyPath) {
            $compassProcessArgs = array_merge(explode(' ', $this->rubyPath), $compassProcessArgs);
        }

        $pb = new ProcessBuilder($compassProcessArgs);
        $pb->inheritEnvironmentVariables();

        if ($this->force) {
            $pb->add('--force');
        }

        if ($this->style) {
            $pb->add('--output-style')->add($this->style);
        }

        if ($this->quiet) {
            $pb->add('--quiet');
        }

        if ($this->boring) {
            $pb->add('--boring');
        }

        if ($this->noLineComments) {
            $pb->add('--no-line-comments');
        }

        // these two options are not passed into the config file
        // because like this, compass adapts this to be xxx_dir or xxx_path
        // whether it's an absolute path or not
        if ($this->imagesDir) {
            $pb->add('--images-dir')->add($this->imagesDir);
        }

        if ($this->javascriptsDir) {
            $pb->add('--javascripts-dir')->add($this->javascriptsDir);
        }

        // options in config file
        $optionsConfig = array();

        if (!empty($loadPaths)) {
            $optionsConfig['additional_import_paths'] = $loadPaths;
        }

        if ($this->unixNewlines) {
            $optionsConfig['sass_options']['unix_newlines'] = true;
        }

        if ($this->debugInfo) {
            $optionsConfig['sass_options']['debug_info'] = true;
        }

        if ($this->cacheLocation) {
            $optionsConfig['sass_options']['cache_location'] = $this->cacheLocation;
        }

        if ($this->noCache) {
            $optionsConfig['sass_options']['no_cache'] = true;
        }

        if ($this->httpPath) {
            $optionsConfig['http_path'] = $this->httpPath;
        }

        if ($this->httpImagesPath) {
            $optionsConfig['http_images_path'] = $this->httpImagesPath;
        }

        if ($this->httpGeneratedImagesPath) {
            $optionsConfig['http_generated_images_path'] = $this->httpGeneratedImagesPath;
        }

        if ($this->generatedImagesPath) {
            $optionsConfig['generated_images_path'] = $this->generatedImagesPath;
        }

        if ($this->httpJavascriptsPath) {
            $optionsConfig['http_javascripts_path'] = $this->httpJavascriptsPath;
        }

        // options in configuration file
        if (count($optionsConfig)) {
            $config = array();
            foreach ($this->plugins as $plugin) {
                $config[] = sprintf("require '%s'", addcslashes($plugin, '\\'));
            }
            foreach ($optionsConfig as $name => $value) {
                if (!is_array($value)) {
                    $config[] = sprintf('%s = "%s"', $name, addcslashes($value, '\\'));
                } elseif (!empty($value)) {
                    $config[] = sprintf('%s = %s', $name, $this->formatArrayToRuby($value));
                }
            }

            $configFile = tempnam($tempDir, 'assetic_compass');
            file_put_contents($configFile, implode("\n", $config)."\n");
            $pb->add('--config')->add($configFile);
        }

        $pb->add('--sass-dir')->add('')->add('--css-dir')->add('');

        // compass choose the type (sass or scss from the filename)
        if (null !== $this->scss) {
            $type = $this->scss ? 'scss' : 'sass';
        } elseif ($path) {
            // FIXME: what if the extension is something else?
            $type = pathinfo($path, PATHINFO_EXTENSION);
        } else {
            $type = 'scss';
        }

        $tempName = tempnam($tempDir, 'assetic_compass');
        unlink($tempName); // FIXME: don't use tempnam() here

        // input
        $input = $tempName.'.'.$type;

        // work-around for https://github.com/chriseppstein/compass/issues/748
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $input = str_replace('\\', '/', $input);
        }

        $pb->add($input);
        file_put_contents($input, $asset->getContent());

        // output
        $output = $tempName.'.css';

        if ($this->homeEnv) {
            // it's not really usefull but... https://github.com/chriseppstein/compass/issues/376
            $pb->setEnv('HOME', sys_get_temp_dir());
        }

        $proc = $pb->getProcess();
        $code = $proc->run();

        if (0 < $code) {
            unlink($input);
            if (isset($configFile)) {
                unlink($configFile);
            }

            throw FilterException::fromProcess($proc)->setInput($asset->getContent());
        }

        $asset->setContent(file_get_contents($output));

        unlink($input);
        unlink($output);
        if (isset($configFile)) {
            unlink($configFile);
        }
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    private function formatArrayToRuby($array)
    {
        $output = array();

        // does we have an associative array ?
        if (count(array_filter(array_keys($array), "is_numeric")) != count($array)) {
            foreach ($array as $name => $value) {
                $output[] = sprintf('    :%s => "%s"', $name, addcslashes($value, '\\'));
            }
            $output = "{\n".implode(",\n", $output)."\n}";
        } else {
            foreach ($array as $name => $value) {
                $output[] = sprintf('    "%s"', addcslashes($value, '\\'));
            }
            $output = "[\n".implode(",\n", $output)."\n]";
        }

        return $output;
    }
}
