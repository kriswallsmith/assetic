<?php

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Loads SCSS/Compass files.
 *
 * @see http://beta.compass-style.org/help/tutorials/command-line/
 * @see https://github.com/miracle2k/webassets/blob/master/src/webassets/filter/compass.py
 * @author Maxime Thirouin <dev@moox.fr>
 */
class CompassFilter implements FilterInterface
{
    /*
    $ compass compile -h
    Options:
        --time                       Display compilation times.
    -r, --require LIBRARY            Require the given ruby LIBRARY before running commands.
                                       This is used to access compass plugins without having a
                                       project configuration file.
    -l, --load FRAMEWORK_DIR         Load the framework or extensions found in the FRAMEWORK directory.
    -L, --load-all FRAMEWORKS_DIR    Load all the frameworks or extensions found in the FRAMEWORKS_DIR directory.
    -q, --quiet                      Quiet mode.
        --trace                      Show a full stacktrace on error
        --force                      Allows some failing commands to succeed instead.
        --dry-run                    Dry Run. Tells you what it plans to do.
        --boring                     Turn off colorized output.
    -c, --config CONFIG_FILE         Specify the location of the configuration file explicitly.
        --app APP                    Tell compass what kind of application it is integrating with. E.g. rails
        --sass-dir SRC_DIR           The source directory where you keep your sass stylesheets.
        --css-dir CSS_DIR            The target directory where you keep your css stylesheets.
        --images-dir IMAGES_DIR      The directory where you keep your images.
        --javascripts-dir JS_DIR     The directory where you keep your javascripts.
    -e, --environment ENV            Use sensible defaults for your current environment.
                                       One of: development, production (default)
    -s, --output-style STYLE         Select a CSS output mode.
                                       One of: nested, expanded, compact, compressed
        --relative-assets            Make compass asset helpers generate relative urls to assets.
        --no-line-comments           Disable line comments.
    */
    private $compassPath;
    private $time;
    private $require;
    private $loadPath;
    private $loadAllPath;
    private $quiet;
    private $trace;
    private $force;
    private $dryRun;
    private $boring;
    private $configFile;
    private $sassDir;
    private $imagesDir;
    private $javascriptDir;
    private $environment;
    private $outputStyle = 'expanded';
    private $relativeAsset;
    private $noLineComments;

    public function __construct($compassPath = '/usr/bin/compass')
    {
        $this->compassPath = $compassPath;
    }

    /**
     * @param string $compassPath
     */
    public function setCompassPath ($compassPath)
    {
        $this->compassPath = $compassPath;
    }

    /**
     * @param boolean $time
     */
    public function setTime ($time)
    {
        $this->time = $time;
    }

    /**
     * @param string $require
     */
    public function setRequire ($require)
    {
        $this->require = $require;
    }

    /**
     * @param string $loadPath
     */
    public function setLoadPath ($loadPath)
    {
        $this->loadPath = $loadPath;
    }

    /**
     * @param string $loadAllPath
     */
    public function setLoadAllPath ($loadAllPath)
    {
        $this->loadAllPath = $loadAllPath;
    }

    /**
     * @param boolean $quiet
     */
    public function setQuiet ($quiet)
    {
        $this->quiet = $quiet;
    }

    /**
     * @param boolean $trace
     */
    public function setTrace ($trace)
    {
        $this->trace = $trace;
    }

    /**
     * @param boolean $force
     */
    public function setForce ($force)
    {
        $this->force = $force;
    }

    /**
     * @param boolean $dryRun
     */
    public function setDryRun ($dryRun)
    {
        $this->dryRun = $dryRun;
    }

    /**
     * @param boolean $boring
     */
    public function setBoring ($boring)
    {
        $this->boring = $boring;
    }

    /**
     * @param string $configFile
     */
    public function setConfigFile ($configFile)
    {
        $this->configFile = $configFile;
    }

    /**
     * @param string $sassDir
     */
    public function setSassDir ($sassDir)
    {
        $this->sassDir = $sassDir;
    }

    /**
     * @param string $imagesDir
     */
    public function setImagesDir ($imagesDir)
    {
        $this->imagesDir = $imagesDir;
    }

    /**
     * @param string $javascriptDir
     */
    public function setJavascriptDir ($javascriptDir)
    {
        $this->javascriptDir = $javascriptDir;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment ($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param string $outputStyle
     */
    public function setOutputStyle ($outputStyle)
    {
        $this->outputStyle = $outputStyle;
    }

    /**
     * @param boolean $relativeAsset
     */
    public function setRelativeAsset ($relativeAsset)
    {
        $this->relativeAsset = $relativeAsset;
    }

    /**
     * @param boolean $noLineComments
     */
    public function setNoLineComments ($noLineComments)
    {
        $this->noLineComments = $noLineComments;
    }

    /**
     * Filters an asset after it has been loaded.
     */
    public function filterLoad(AssetInterface $asset)
    {
        $options = array($this->compassPath, 'compile'); // we only compile with this tool

        if ($this->require) {
            $options[] = '--require';
            $options[] = $this->require;
        }

        if ($this->loadPath) {
            $options[] = '--load';
            $options[] = $this->loadPath;
        }

        if ($this->loadAllPath) {
            $options[] = '--load-all';
            $options[] = $this->loadAllPath;
        }

        if ($this->quiet) {
            $options[] = '--quiet';
        }

        if ($this->trace) {
            $options[] = '--trace';
        }

        if ($this->force) {
            $options[] = '--force';
        }

        if ($this->dryRun) {
            $options[] = '--dry-run';
        }

        if ($this->boring) {
            $options[] = '--boring';
        }

        if ($this->configFile) {
            $options[] = '--config';
            $options[] = $this->configFile;
        }

        if ($this->imagesDir) {
            $options[] = '--images-dir';
            $options[] = $this->imagesDir;
        }

        if ($this->javascriptsDir) {
            $options[] = '--javascripts-dir';
            $options[] = $this->javascriptsDir;
        }

        if ($this->environment) {
            $options[] = '--environment';
            $options[] = $this->environment;
        }

        if ($this->outputStyle) {
            $options[] = '--output-style';
            $options[] = $this->outputStyle;
        }

        if ($this->relativeAsset) {
            $options[] = '--relative-asset';
        }

        if ($this->noLineComments) {
            $options[] = '--no-line-comments';
        }

        /*
        Compass currently doesn't take data from stdin, and doesn't allow
        us from stdout either.

        Also, there's a bunch of other issues we need to work around:
         - compass doesn't support given an explict output file, only a
           "--css-dir" output directory.
         - The output filename used is based on the input filename, and
           simply cutting of the length of the "sass_dir" (and changing
           the file extension). That is, compass expects the input
           filename to always be inside the "sass_dir" (which defaults to
           ./src), and if this is not the case, the output filename will
           be gibberish (missing characters in front).

        As a result, we can set both the --sass-dir and --css-dir
        options properly, so we can "guess" the final css filename.
        But it's a bad idea, because using not he original sass-dir will
        make the @import (for partials _*.scss files) does not work.
        And we use compass for that right ?

        So we just can creating output name that will be used by
        reproducing normal compass method (cutting length) :
        If basename is shorter thant sass dir, we make it longer to prevent output filename to be ".css"
        (because just cutting length will make a empty string)

        Exemple:
        Your sass dir is /home/user/verylongname/oulala/stylesheets/
        Your temp_dir is /var/tmp
        Your css dir WILL BE /var/tmp
        So your temp scss file will be /var/tmp/{tempnam}.scss
        => output file will be /var/tmp/{tempnam}.scss - .scss - /home/user/verylongname/oulala/stylesheets/ == empty path !

        So we add some chars to make the tempnam longer
        ...
        /var/tmp/longerthanthesassDir/longeryes/longer{tempnam}.scss .scss - /home/user/verylongname/oulala/stylesheets/ == not empty path :)

        Maybe it's a brutal approch...
        */

        $tempPath = sys_get_temp_dir();
        $tempKey = 'assetic_compass';
        $simulatedBasename = $tempPath . '/' . $tempKey;
        $diff = strlen($simulatedBasename) - strlen($this->sassDir);
        $basename = '';
        if ($diff < 0)
        {
            $tempKey .= str_repeat('-', -$diff);
        }
        $basename .= tempnam($tempPath, $tempKey);
        $input .=  $basename . '.scss';

        $output = $tempPath . '/' . substr($basename, strlen($this->sassDir)) . '.css';

        file_put_contents($input, $asset->getBody());

        $options[] = '--sass-dir';
        $options[] = $this->sassDir;
        $options[] = '--css-dir';
        $options[] = $tempPath;

        $options[] = $input;

        $cmd = implode(' ', array_map('escapeshellarg', $options));
        // todo: check for a valid return code
        $commandOutput = shell_exec($cmd);

        $asset->setBody(file_get_contents($output));

        // cleanup
        unlink($input);
        unlink($output);
    }

    public function filterDump(AssetInterface $asset)
    {

    }
}
