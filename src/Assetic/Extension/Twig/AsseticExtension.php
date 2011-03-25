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

    public function __construct(AssetFactory $factory, $debug = false)
    {
        $this->factory = $factory;
        $this->debug = $debug;
    }

    public function getTokenParsers()
    {
        return array(
            new AsseticTokenParser($this->factory, 'javascripts', 'js/*.js', $this->debug),
            new AsseticTokenParser($this->factory, 'stylesheets', 'css/*.css', $this->debug),
            new AsseticTokenParser($this->factory, 'image', 'images/*', $this->debug, true),
        );
    }

    public function getName()
    {
        return 'assetic';
    }
}
