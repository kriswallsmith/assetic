<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Twig;

use Assetic\Cache\ConfigCache;
use Assetic\Factory\Loader\FormulaLoaderInterface;
use Assetic\Factory\Resource\ResourceInterface;

/**
 * Loads asset formulae from Twig templates.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class TwigFormulaLoader implements FormulaLoaderInterface
{
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function load(ResourceInterface $resource)
    {
        $name = (string) $resource;

        // load the template to ensure what's in the cache is fresh
        $this->twig->loadTemplate($name);

        // fetch the formulae from the config cache
        return $this->twig->getExtension('assetic')->getConfigCache()->get($name);
    }
}
