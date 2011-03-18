<?php

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\Sass\SassFilter;

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
    private $compassPath;
    private $loadPath;
    private $loadAllPath;
    private $sassDir;
    private $imagesDir;
    private $outputStyle = SassFilter::STYLE_EXPANDED;

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
     * Filters an asset after it has been loaded.
     */
    public function filterLoad(AssetInterface $asset)
    {
        $options = array(
            $this->compassPath, 'compile', // we only compile with this tool
            '--quiet',
            '--boring'
        );

        if ($this->loadPath) {
            $options[] = '--load';
            $options[] = $this->loadPath;
        }

        if ($this->loadAllPath) {
            $options[] = '--load-all';
            $options[] = $this->loadAllPath;
        }

        if ($this->imagesDir) {
            $options[] = '--images-dir';
            $options[] = $this->imagesDir;
        }

        if ($this->outputStyle) {
            $options[] = '--output-style';
            $options[] = $this->outputStyle;
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
        
        if (empty($this->sassDir) and is_a($asset, 'Assetic\Asset\FileAsset')) {
            $this->sassDir = dirname($asset->getPath());
        }
        else
        {
        	$this->sassDir = realpath($this->sassDir);
        }

        $tempPath = sys_get_temp_dir();
        $tempKey = 'assetic_compass';
        $simulatedBasename = $tempPath . '/' . $tempKey;
        $diff = strlen($simulatedBasename) - strlen($this->sassDir);
        $basename = '';
        if ($diff < 0)
        {
            $tempKey .= str_repeat('-', -$diff + 1);
        }
        $basename .= tempnam($tempPath, $tempKey);
        $input =  $basename . '.scss';
        
        // +1 if for the trailing slash which if removed by compass
        $output = $tempPath . '/' . substr($basename, strlen($this->sassDir) + 1) . '.css';

        file_put_contents($input, $asset->getContent());

        $options[] = '--sass-dir';
        $options[] = $this->sassDir;
        $options[] = '--css-dir';
        $options[] = $tempPath;

        $options[] = $input;

        $cmd = implode(' ', array_map('escapeshellarg', $options));
        // @todo: check for a valid return code ?
        $commandOutput = shell_exec($cmd);

        //if(!is_readable($output)) {
            // @todo throw an Exception if there is no file ?
        //}
        
        $asset->setContent(file_get_contents($output));

        // cleanup
        unlink($input);
        unlink($output);
    }

    public function filterDump(AssetInterface $asset)
    {

    }
}
