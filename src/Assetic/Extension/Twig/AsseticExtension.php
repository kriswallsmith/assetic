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
    protected $debug;
    protected $defaultJavascriptsOutput;
    protected $defaultStylesheetsOutput;

    public function __construct(AssetFactory $factory, $debug = false, $defaultJavascriptsOutput = 'js/*.js', $defaultStylesheetsOutput = 'css/*.css')
    {
        $this->factory = $factory;
        $this->debug = $debug;
        $this->defaultJavascriptsOutput = $defaultJavascriptsOutput;
        $this->defaultStylesheetsOutput = $defaultStylesheetsOutput;
    }

    public function getTokenParsers()
    {
        return array(
            new AsseticTokenParser($this->factory, $this->debug),
            new AsseticTokenParser($this->factory, $this->debug, $this->defaultJavascriptsOutput, 'javascripts'),
            new AsseticTokenParser($this->factory, $this->debug, $this->defaultStylesheetsOutput, 'stylesheets'),
        );
    }

    public function getName()
    {
        return 'assetic';
    }
}
