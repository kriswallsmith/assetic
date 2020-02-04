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
use Assetic\Util\CssUtils;

/**
 * Loads SCSS files using the PHP implementation of scss, scssphp.
 *
 * Scss files are mostly compatible, but there are slight differences.
 *
 * @link https://scssphp.github.io/scssphp/
 *
 * @author Bart van den Burg <bart@samson-it.nl>
 */
class ScssphpFilter implements DependencyExtractorInterface
{
    private $compass = false;
    private $importPaths = array();
    private $customFunctions = array();
    private $formatter;
    private $variables = array();

    public function enableCompass($enable = true)
    {
        $this->compass = (Boolean) $enable;
    }

    public function isCompassEnabled()
    {
        return $this->compass;
    }

    public function setFormatter($formatter)
    {
        $legacyFormatters = array(
            'scss_formatter' => array(
                'leafo' => 'Leafo\ScssPhp\Formatter\Expanded',
                'scssphp' => 'ScssPhp\ScssPhp\Formatter\Expanded'
            ),
            'scss_formatter_nested' => array(
                'leafo' => 'Leafo\ScssPhp\Formatter\Nested',
                'scssphp' => 'ScssPhp\ScssPhp\Formatter\Nested'
            ),
            'scss_formatter_compressed' => array(
                'leafo' => 'Leafo\ScssPhp\Formatter\Compressed',
                'scssphp' => 'ScssPhp\ScssPhp\Formatter\Compressed'
            ),
            'scss_formatter_crunched' => array(
                'leafo' => 'Leafo\ScssPhp\Formatter\Crunched',
                'scssphp' => 'ScssPhp\ScssPhp\Formatter\Crunched'
            ),
        );

        if (isset($legacyFormatters[$formatter])) {
            if (class_exists($legacyFormatters[$formatter]['scssphp'])) {
                $legacyFormatter = $legacyFormatters[$formatter]['scssphp'];
            } else {
                $legacyFormatter = $legacyFormatters[$formatter]['leafo'];
            }
            @trigger_error(sprintf('The scssphp formatter `%s` is deprecated. Use `%s` instead.', $formatter, $legacyFormatter), E_USER_DEPRECATED);

            $formatter = $legacyFormatter;
        }

        $this->formatter = $formatter;
    }

    public function setVariables(array $variables)
    {
        $this->variables = $variables;
    }

    public function addVariable($variable)
    {
        $this->variables[] = $variable;
    }

    public function setImportPaths(array $paths)
    {
        $this->importPaths = $paths;
    }

    public function addImportPath($path)
    {
        $this->importPaths[] = $path;
    }

    public function registerFunction($name, $callable)
    {
        $this->customFunctions[$name] = $callable;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $sc = $this->newCompiler();

        if ($this->compass) {
            new \scss_compass($sc);
        }

        if ($dir = $asset->getSourceDirectory()) {
            $sc->addImportPath($dir);
        }

        foreach ($this->importPaths as $path) {
            $sc->addImportPath($path);
        }

        foreach ($this->customFunctions as $name => $callable) {
            $sc->registerFunction($name, $callable);
        }

        if ($this->formatter) {
            $sc->setFormatter($this->formatter);
        }

        if (!empty($this->variables)) {
            $sc->setVariables($this->variables);
        }

        $asset->setContent($sc->compile($asset->getContent()));
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    public function getChildren(AssetFactory $factory, $content, $loadPath = null)
    {
        $sc = $this->newCompiler();
        if ($loadPath !== null) {
            $sc->addImportPath($loadPath);
        }

        foreach ($this->importPaths as $path) {
            $sc->addImportPath($path);
        }

        $children = array();
        foreach (CssUtils::extractImports($content) as $match) {
            $file = $sc->findImport($match);
            if ($file) {
                $children[] = $child = $factory->createAsset($file, array(), array('root' => $loadPath));
                $child->load();
                $children = array_merge($children, $this->getChildren($factory, $child->getContent(), $loadPath));
            }
        }

        return $children;
    }

    protected function newCompiler()
    {
        if (class_exists('ScssPhp\ScssPhp\Compiler')) {
            return new \ScssPhp\ScssPhp\Compiler();
        }
        return new \Leafo\ScssPhp\Compiler();
    }
}
