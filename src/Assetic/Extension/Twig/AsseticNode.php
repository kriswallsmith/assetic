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

use Assetic\Asset\AssetInterface;

class AsseticNode extends BaseAsseticNode
{
    /**
     * Constructor.
     *
     * Available attributes:
     *
     *  * debug:    The debug mode
     *  * combine:  Whether to combine assets
     *  * var_name: The name of the variable to expose to the body node
     *
     * @param AssetInterface      $asset      The asset
     * @param \Twig_NodeInterface $body       The body node
     * @param array               $inputs     An array of input strings
     * @param array               $filters    An array of filter strings
     * @param string              $name       The name of the asset
     * @param array               $attributes An array of attributes
     * @param integer             $lineno     The line number
     * @param string              $tag        The tag name
     */
    public function __construct(AssetInterface $asset, \Twig_NodeInterface $body, array $inputs, array $filters, $name, array $attributes = array(), $lineno = 0, $tag = null)
    {
        $attributes = array_replace(
            $attributes,
            array('asset' => $asset)
        );

        parent::__construct($body, $inputs, $filters, $name, $attributes, $lineno, $tag);
    }
}
