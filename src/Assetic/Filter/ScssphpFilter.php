<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
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
    
    private $compileParsedFilesCache = array();
    
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
        $this->compileParsedFilesCachex[sha1($asset->getSourceDirectory().$asset->getContent())] = $this->scssCompiler->getParsedFiles();
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
        $cacheId = sha1($loadPath.$content);
        if (isset($this->compileParsedFilesCache[$cacheId])) {
            $parsedFiles = $this->compileParsedFilesCache[$cacheId];
        } else {
            $this->resetScssCompiler();

            if (null !== $loadPath) {
                $this->addImportPath($loadPath);
            }

            $this->compile( $content );

            $parsedFiles = $this->scssCompiler->getParsedFiles();
            $this->compileParsedFilesCache[$cacheId] = $parsedFiles;
        }

        $children = array();
        foreach ($parsedFiles as $file) {
            // We don't want assetic to compile this $file and do the same with all its children
            // What we care is only that assetic picks the children lastModified date
            $asset = new StringAsset('');
            $asset->setLastModified(filemtime($file));

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
