<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Twig;

use Assetic\Factory\AssetFactory;

class AsseticExtension extends \Twig_Extension
{
    protected $factory;
    protected $functions;

    public function __construct(AssetFactory $factory, $functions = array())
    {
        $this->factory = $factory;
        $this->functions = array();

        if(!empty($functions)) {
            foreach ($functions as $function => $options) {
                if (is_integer($function) && is_string($options)) {
                    $this->functions[$options] = $options;
                } else {
                    $this->functions[$function] = $options;
                }
            }
        }
    }

    public function getTokenParsers()
    {
        return array(
            new AsseticTokenParser($this->factory, 'javascripts', 'js/*.js'),
            new AsseticTokenParser($this->factory, 'stylesheets', 'css/*.css'),
            new AsseticTokenParser($this->factory, 'image', 'images/*', true),
        );
    }

    public function getFunctions()
    {
        $functions = array();
        foreach ($this->functions as $function => $filter) {
            $functions[$function] = new AsseticFilterFunction($function);
        }

        return $functions;
    }

    public function getGlobals()
    {
        return array(
            'assetic' => array('debug' => $this->factory->isDebug()),
        );
    }

    public function getFilterInvoker($function)
    {
        return new AsseticFilterInvoker($this->factory, $this->functions[$function]);
    }

    public function getName()
    {
        return 'assetic';
    }
}
