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
use Assetic\Factory\Resource\IteratorResourceInterface;
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

    public function load(ResourceInterface $resources)
    {
        $cache = $this->twig->getExtension('assetic')->getConfigCache();

        if (!$resources instanceof IteratorResourceInterface) {
            $resources = array($resources);
        }

        $formulae = array();
        foreach ($resources as $resource) {
            $name = (string) $resource;

            try {
                $this->loadTemplate($name, $cache);
            } catch (\Exception $e) {
                // ignore twig errors (none of our business)
                continue;
            }

            // fetch the formulae from the config cache
            $formulae += $cache->get($name);
        }

        return $formulae;
    }

    private function loadTemplate($name, ConfigCache $cache)
    {
        // load the template to ensure what's in the cache is fresh
        $this->twig->loadTemplate($name);

        // force a parse if necessary
        if (!$cache->has($name)) {
            $source = $this->twig->getLoader()->getSource($name);
            $tokens = $this->twig->tokenize($source, $name);
            $nodes  = $this->twig->parse($tokens);
        }
    }
}
