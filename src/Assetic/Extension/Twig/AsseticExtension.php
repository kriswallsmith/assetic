<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Twig;

use Assetic\Factory\AssetFactory;

class AsseticExtension extends \Twig_Extension
{
    private $factory;
    private $debug;

    public function __construct(AssetFactory $factory, $debug = false)
    {
        $this->factory = $factory;
        $this->debug = $debug;
    }

    public function getTokenParsers()
    {
        return array(
            new TokenParser($this->factory, $this->debug),
            new TokenParser($this->factory, $this->debug, 'js/*.js', 'javascripts'),
            new TokenParser($this->factory, $this->debug, 'css/*.css', 'stylesheets'),
        );
    }

    public function getName()
    {
        return 'assetic';
    }
}
