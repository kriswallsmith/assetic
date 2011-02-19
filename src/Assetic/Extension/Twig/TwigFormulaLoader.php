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

    public function supports(ResourceInterface $resource)
    {
        return $resource instanceof TwigResource;
    }

    public function load(ResourceInterface $resource)
    {
        $tokens = $this->twig->tokenize($resource->getContent());
        $nodes  = $this->twig->parse($tokens);

        return $this->loadNode($nodes);
    }

    /**
     * Loads assets from the supplied node.
     *
     * @return array An array of asset formulae indexed by name
     */
    private function loadNode(\Twig_Node $node)
    {
        $assets = array();

        if ($node instanceof AsseticNode) {
            $assets[$node->getAttribute('asset_name')] = array(
                $node->getAttribute('source_urls'),
                $node->getAttribute('filter_names'),
                array(
                    'output' => $node->getAttribute('target_url'),
                    'name'   => $node->getAttribute('asset_name'),
                    'debug'  => $node->getAttribute('debug'),
                ),
            );
        }

        foreach ($node as $child) {
            if ($child instanceof \Twig_Node) {
                $assets += $this->loadNode($child);
            }
        }

        return $assets;
    }
}
