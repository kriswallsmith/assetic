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

/**
 * Watches for Assetic nodes and stores formulae to a cache.
 */
class AsseticNodeVisitor implements \Twig_NodeVisitorInterface
{
    private $cache;
    private $formulae;

    public function __construct(ConfigCache $cache)
    {
        $this->cache = $cache;
    }

    public function enterNode(\Twig_NodeInterface $node, \Twig_Environment $env)
    {
        if ($node instanceof \Twig_Node_Module) {
            $this->formulae = array();
        } elseif ($node instanceof AsseticNode) {
            $this->formulae[$node->getAttribute('name')] = array(
                $node->getAttribute('inputs'),
                $node->getAttribute('filters'),
                array(
                    'output'  => $node->getAttribute('asset')->getTargetPath(),
                    'name'    => $node->getAttribute('name'),
                    'debug'   => $node->getAttribute('debug'),
                    'combine' => $node->getAttribute('combine'),
                    'vars'    => $node->getAttribute('vars'),
                ),
            );
        } elseif ($node instanceof \Twig_Node_Expression_Function) {
            $name = version_compare(\Twig_Environment::VERSION, '1.2.0-DEV', '<')
                ? $node->getNode('name')->getAttribute('name')
                : $node->getAttribute('name');

            if ($env->getFunction($name) instanceof AsseticFilterFunction) {
                $arguments = array();
                foreach ($node->getNode('arguments') as $argument) {
                    $arguments[] = eval('return '.$env->compile($argument).';');
                }

                $invoker = $env->getExtension('assetic')->getFilterInvoker($name);

                $inputs  = isset($arguments[0]) ? (array) $arguments[0] : array();
                $filters = $invoker->getFilters();
                $options = array_replace($invoker->getOptions(), isset($arguments[1]) ? $arguments[1] : array());

                if (!isset($options['name'])) {
                    $options['name'] = $invoker->getFactory()->generateAssetName($inputs, $filters, $options);
                }

                $this->formulae[$options['name']] = array($inputs, $filters, $options);
            }
        }

        return $node;
    }

    public function leaveNode(\Twig_NodeInterface $node, \Twig_Environment $env)
    {
        if ($node instanceof \Twig_Node_Module) {
            $this->cache->set($node->getAttribute('filename'), $this->formulae);
            $this->formulae = array();
        }

        return $node;
    }

    public function getPriority()
    {
        return 0;
    }
}
