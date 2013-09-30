<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Factory\AssetFactory;
use Assetic\Asset\FileAsset;

/**
 * Loads SCSS files using the PHP implementation of scss, scssphp.
 *
 * Scss files are mostly compatible, but there are slight differences.
 *
 * @link http://leafo.net/scssphp/
 *
 * @author Bart van den Burg <bart@samson-it.nl>
 */
class ScssphpFilter implements DependencyExtractorInterface
{
    private $compass = false;

    private $importPaths = array();

    private $customFunctions = array(); 
    
    /**
     * @var \scssc
     */
    private $scssCompiler;
    

    public function enableCompass($enable = true)
    {
        $this->compass = (Boolean) $enable;
    }

    public function isCompassEnabled()
    {
        return $this->compass;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $this->resetScssCompiler();
        if ($dir = $asset->getSourceDirectory()) {
            $this->scssCompiler->addImportPath($dir);
        }
        $asset->setContent($this->compile($asset->getContent()));
    }

    public function setImportPaths(array $paths)
    {
        $this->importPaths = $paths;
    }

    public function addImportPath($path)
    {
        $this->importPaths[] = $path;
    }

    public function registerFunction($name,$callable)
    {
        $this->customFunctions[$name] = $callable;
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        $this->resetScssCompiler();
        $this->compile( $content );
        $children = array();
        foreach($this->scssCompiler->getParsedFiles() as $file){
            $asset = new FileAsset($file);
            $asset->ensureFilter($this);
            $children[] = $asset;
        }
        return $children;
    }
    
    private function compile( $content )
    {
        if ($this->compass) {
            new \scss_compass($this->scssCompiler);
        }
        foreach ($this->importPaths as $path) {
            $this->scssCompiler->addImportPath($path);
        }

        foreach($this->customFunctions as $name=>$callable){
            $this->scssCompiler->registerFunction($name,$callable);
        }
        return $this->scssCompiler->compile( $content );
    }
    
    private function resetScssCompiler()
    {
        $this->scssCompiler = new \scssc();
    }
}
