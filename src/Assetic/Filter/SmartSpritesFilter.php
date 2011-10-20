<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Assetic\Util\ProcessBuilder;

/**
 * SmartSprites filter
 *
 * @author Robert Schönthal <seroscho@googlemail.com>
 */
class SmartSpritesFilter implements FilterInterface
{
    private $path;
    private $javaPath;
    private $charset;
    private $document_root;
    private $loglevel;
    private $sprites_depth;
    private $sprites_ie6;
    private $css_file_suffix;

    public function __construct($path, $javaPath = '/usr/bin/java')
    {
        if(!is_readable($path)){
            throw new \InvalidArgumentException('smartsprites path invalid'.$path);
        }
        
        $this->path = $path;
        $this->javaPath = $javaPath;                
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function setLogLevel($loglevel)
    {
        $this->loglevel = $loglevel;
    }
    
    public function setDocumentRoot($root)
    {
        $this->document_root = $root;
    }
    
    public function setSpritesDepth($depth)
    {
        $this->sprites_depth = $depth;
    }
    
    public function setSpritesIe6($ie6)
    {
        $this->sprites_ie6 = $ie6;
    }

    public function setCssFileSuffix($suffix)
    {
        $this->css_file_suffix = $suffix;
    }
    
    /**
     * @{InheritDoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        $spritefile = $this->getSprite($asset);
        
        //write sprite
        if(is_writable(pathinfo($spritefile,PATHINFO_DIRNAME)) && 
           strpos($asset->getContent(), '/** sprite:') !== false){
            $this->write($asset);
        //sprited variant exists but not writable (mostly from apache if permissions are restrictive)
        }elseif(is_readable($spritefile)){
            $asset->setContent(file_get_contents($spritefile));
        }
    }
    
    /**
     * @{InheritDoc}
     */
    public function filterDump(AssetInterface $asset)
    {
    }
    
    /**
     * compile a sprited file
     * 
     * @param AssetInterface $asset 
     */
    protected function write(AssetInterface $asset)
    {        
        $pb = new ProcessBuilder();
        $pb
            ->inheritEnvironmentVariables()
            ->setWorkingDirectory($this->path)
            ->add($this->javaPath)
            ->add('-Djava.awt.headless=true')
            ->add('-Djava.ext.dirs=lib')
            ->add('org.carrot2.labs.smartsprites.SmartSprites')
        ;

        if (null !== $this->charset) {
            $pb->add('--css-file-encoding')->add($this->charset);
        }
        
        if ($this->loglevel) {
            $pb->add('--log-level')->add($this->loglevel);
        }
        
        if(!$this->document_root)
        {
            $this->document_root = dirname($asset->getSourceRoot());
        }
        
        $pb->add('--document-root-dir-path')->add($this->document_root);

        if($this->sprites_depth)
        {
            $pb->add('--sprite-png-depth')->add($this->sprites_depth);
        }

        if($this->sprites_ie6)
        {
            $pb->add('--sprite-png-ie6')->add($this->sprites_ie6);
        }
        
        if($this->css_file_suffix)
        {
            $pb->add('--css-file-suffix')->add($this->css_file_suffix);
        }
                
        $pb->add('--css-files')->add($asset->getSourceRoot().'/'.$asset->getSourcePath());

        $proc = $pb->getProcess();
        $code = $proc->run();

        if ($code < 0 || strpos($proc->getOutput(), 'ERROR:') !== false) {
            throw new \RuntimeException($proc->getOutput());
        }
        
        if('INFO' == $this->loglevel && 'cli' == php_sapi_name()){
            echo $proc->getOutput();
        }
        
        $file = $this->getSprite($asset);
        
        $asset->setContent(sprintf("/*\n%s*/\n%s",$proc->getOutput(),file_get_contents($file)));
    }
    
    /**
     * defines the sprited file
     * 
     * @param AssetInterface $asset
     * @return string 
     */
    protected function getSprite(AssetInterface $asset)
    {
        $filename = substr($asset->getSourcePath(),0, stripos($asset->getSourcePath(), '.')).$this->css_file_suffix.'.css';
        $file = $asset->getSourceRoot().'/'.$filename;
                
        return $file;
    }
}
