<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Twig;

use Assetic\Factory\Loader\FormulaLoaderInterface;
use Assetic\Factory\Resource\ResourceInterface;
use Psr\Log\LoggerInterface;

/**
 * Loads asset formulae from Twig templates.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class TwigFormulaLoader implements FormulaLoaderInterface
{
    private $twig;
    private $logger;

    public function __construct(\Twig_Environment $twig, LoggerInterface $logger = null)
    {
        $this->twig = $twig;
        $this->logger = $logger;
    }

    public function load(ResourceInterface $resource)
    {
        try {
            $tokens = $this->twig->tokenize(new \Twig_Source($resource->getContent(), (string) $resource));
            $nodes  = $this->twig->parse($tokens);
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error(sprintf('The template "%s" contains an error: %s', $resource, $e->getMessage()));
            }

            return [];
        }

        return $this->loadNode($nodes);
    }

    /**
     * Loads assets from the supplied node.
     *
     * @param \Twig_Node $node
     *
     * @return array An array of asset formulae indexed by name
     */
    private function loadNode(\Twig_Node $node)
    {
        $formulae = [];

        if ($node instanceof AsseticNode) {
            $formulae[$node->getAttribute('name')] = [
                $node->getAttribute('inputs'),
                $node->getAttribute('filters'),
                [
                    'output'  => $node->getAttribute('asset')->getTargetPath(),
                    'name'    => $node->getAttribute('name'),
                    'debug'   => $node->getAttribute('debug'),
                    'combine' => $node->getAttribute('combine'),
                    'vars'    => $node->getAttribute('vars'),
                ],
            ];
        } elseif ($node instanceof AsseticFilterNode) {
            $name = $node->getAttribute('name');

            $arguments = [];
            foreach ($node->getNode('arguments') as $argument) {
                $arguments[] = eval('return '.$this->twig->compile($argument).';');
            }

            $invoker = $this->twig->getExtension('Assetic\Extension\Twig\AsseticExtension')->getFilterInvoker($name);

            $inputs  = (array) $arguments[0] ?? [];
            $filters = $invoker->getFilters();
            $options = array_replace($invoker->getOptions(), $arguments[1] ?? []);

            if (!isset($options['name'])) {
                $options['name'] = $invoker->getFactory()->generateAssetName($inputs, $filters, $options);
            }

            $formulae[$options['name']] = [$inputs, $filters, $options];
        }

        foreach ($node as $child) {
            if ($child instanceof \Twig_Node) {
                $formulae += $this->loadNode($child);
            }
        }

        if ($node->hasAttribute('embedded_templates')) {
            foreach ($node->getAttribute('embedded_templates') as $child) {
                $formulae += $this->loadNode($child);
            }
        }

        return $formulae;
    }
}
