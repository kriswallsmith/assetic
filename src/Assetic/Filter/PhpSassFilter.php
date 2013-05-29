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

/**
 * Loads SASS/SCSS files using the PHP implementation of phpsass.
 *
 * @author Anthon Pang <apang@softwaredevelopment.ca>
 *
 * @link https://github.com/richthegeek/phpsass
 */
class PhpSassFilter implements FilterInterface
{
    /**
     * @var boolean
     */
    private $compass = false;

    /**
     * SassParser defaults to SassRenderer::NESTED if unspecified
     *
     * @var string
     */
    private $style = null;

    /**
     * @var array
     */
    private $options = null;

    /**
     * @var array
     */
    private $loadPaths = array();

    /**
     * Set phpsass options
     *
     * @param array $options
     *
     * @return \Assetic\Filter\PhpSassFilter
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Adds a load path to the paths used by phpsass
     *
     * @param string $path Load Path
     *
     * @return \Assetic\Filter\PhpSassFilter
     */
    public function addLoadPath($path)
    {
        $this->loadPaths[] = $path;

        return $this;
    }

    /**
     * Enable/disable compass
     *
     * @param boolean $enable
     *
     * @return \Assetic\Filter\PhpSassFilter
     */
    public function setCompass($enable = true)
    {
        $this->compass = (Boolean) $enable;

        return $this;
    }

    /**
     * Set style
     *
     * @param string $style
     *
     * @return \Assetic\Filter\PhpSassFilter
     */
    public function setStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Retrieve list of functions exposed by SassParser extensions
     *
     * @param array $extensions
     *
     * @return array
     */
    private function prepareFunctions(array $extensions)
    {
        $output = array();

        $reflection = new \ReflectionClass('SassParser');
        $directory = dirname($reflection->getFileName()) . DIRECTORY_SEPARATOR . 'Extensions';

        foreach ($extensions as $extension) {
            $name = explode('/', $extension, 2);
            $namespace = ucwords(preg_replace('/[^0-9a-z]+/', '_', strtolower(array_shift($name))));
            $extensionPath = $directory . DIRECTORY_SEPARATOR . $namespace . DIRECTORY_SEPARATOR . $namespace . '.php';

            if (file_exists($extensionPath)) {
                require_once($extensionPath);

                $namespace = $namespace . '::';
                $function = 'getFunctions';
                $output = array_merge($output, call_user_func($namespace . $function, $namespace));
            }
        }

        return $output;
    }

    /**
     * Filter asset
     *
     * @param \Assetic\Asset\AssetInterface $asset
     */
    public function filterLoad(AssetInterface $asset)
    {
        if (!isset($this->options['filename'])) {
            $this->options['filename'] = array(
                'dirname' => $asset->getSourceRoot(),
                'basename' => $asset->getSourcePath(),
            );
        }

        $this->options['load_paths'][] = $asset->getSourceRoot();
        $this->options['load_paths']  += $this->loadPaths;

        if ($this->compass) {
            // $this->options['extensions'] not yet supported by SassParser
            $functions = $this->prepareFunctions(array('Compass'));

            if (!isset($this->options['functions'])) {
                $this->options['functions'] = $functions;
            } elseif (!in_array('Compass', $this->options['functions'])) {
                $this->options['functions'] += $functions;
            }
        }

        if ($this->style) {
            $this->options['style'] = $this->style;
        }

        $parser = new \SassParser($this->options);

        $asset->setContent($parser->toCss($asset->getContent(), false));
    }

    /**
     * Filter asset
     *
     * @param \Assetic\Asset\AssetInterface $asset
     */
    public function filterDump(AssetInterface $asset)
    {
    }
}
