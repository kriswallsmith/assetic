<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter\GoogleClosure;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Assetic\Util\ProcessBuilder;

/**
 * Filter for the Google Closure Stylesheets Compiler JAR.
 *
 * @author Matthias Krauser <matthias@krauser.eu>
 */
class StylesheetCompilerJarFilter implements FilterInterface
{
    private $jarPath;
    private $javaPath;

    protected $allowUnrecognizedFunctions;
    protected $allowedNonStandardFunctions;
    protected $copyrightNotice;
    protected $define;
    protected $excludeClassesFromRenaming;
    protected $gssFunctionMapProvider;
    protected $inputOrientation;
    protected $outputOrientation;
    protected $outputRenamingMap;
    protected $outputRenamingMapFormat;
    protected $prettyPrint;
    protected $rename;
    
    public function __construct($jarPath, $javaPath = '/usr/bin/java')
    {
        $this->jarPath = $jarPath;
        $this->javaPath = $javaPath;
    }

    public function setAllowUnrecognizedFunctions($allowUnrecognizedFunctions)
    {
        $this->allowUnrecognizedFunctions = $allowUnrecognizedFunctions;
    }

    public function setAllowedNonStandardFunctions($allowNonStandardFunctions)
    {
        $this->allowedNonStandardFunctions = $allowNonStandardFunctions;
    }

    public function setCopyrightNotice($copyrightNotice)
    {
        $this->copyrightNotice = $copyrightNotice;
    }

    public function setDefine($define)
    {
        $this->define = $define;
    }

    public function setGssFunctionMapProvider($gssFunctionMapProvider)
    {
        $this->gssFunctionMapProvider = $gssFunctionMapProvider;
    }
    
    public function setInputOrientation($inputOrientation)
    {
        $this->inputOrientation = $inputOrientation;
    }
    
    public function setOutputOrientation($outputOrientation)
    {
        $this->outputOrientation = $outputOrientation;
    }
       
    public function setPrettyPrint($prettyPrint)
    {
        $this->prettyPrint = $prettyPrint;
    }
    
    public function filterLoad(AssetInterface $asset)
    {
    }
    
    public function filterDump(AssetInterface $asset)
    {
        $cleanup = array();

        $pb = new ProcessBuilder(array(
            $this->javaPath,
            '-jar',
            $this->jarPath,
        ));

        if (null !== $this->allowUnrecognizedFunctions) {
            $pb->add('--allow-unrecognized-functions');
        }

        if (null !== $this->allowedNonStandardFunctions) {
            $pb->add('--allowed_non_standard_functions')->add($this->allowedNonStandardFunctions);
        }

        if (null !== $this->copyrightNotice) {
            $pb->add('--copyright-notice')->add($this->copyrightNotice);
        }

        if (null !== $this->define) {
            $pb->add('--define')->add($this->define);
        }

        if (null !== $this->gssFunctionMapProvider) {
            $pb->add('--gss-function-map-provider')->add($this->gssFunctionMapProvider);
        }

        if (null !== $this->inputOrientation) {
            $pb->add('--input-orientation')->add($this->inputOrientation);
        }
        
        if (null !== $this->outputOrientation) {
            $pb->add('--output-orientation')->add($this->outputOrientation);
        }
        
        if (null !== $this->prettyPrint) {
            $pb->add('--pretty-print');
        }
        
        $pb->add($cleanup[] = $input = tempnam(sys_get_temp_dir(), 'assetic_google_closure_stylesheets_compiler'));
        file_put_contents($input, $asset->getContent());
        
        $proc = $pb->getProcess();
        $code = $proc->run();
        array_map('unlink', $cleanup);

        if (0 < $code) {
            throw new \RuntimeException($proc->getErrorOutput());
        }

        $asset->setContent($proc->getOutput());
    }

}
