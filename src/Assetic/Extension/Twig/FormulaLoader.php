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

/**
 * Loads asset formulae from Twig templates.
 *
 * A formula is an array of arguments for {@link AssetFactory::createAsset()}.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FormulaLoader
{
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Extracts asset formulae from a template.
     *
     * @param string $template The name of the template to load
     *
     * @return array An array of asset formulae indexed by name
     */
    public function load($template)
    {
        $source = $this->twig->getLoader()->getSource($template);
        $tokens = $this->twig->tokenize($source);
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

        if ($node instanceof Node) {
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
