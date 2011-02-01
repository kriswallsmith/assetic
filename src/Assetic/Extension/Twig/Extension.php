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

class Extension extends \Twig_Extension
{
    private $tokenParser;

    /**
     * The token parser is injected because it depends on the asset factory and manager...
     */
    public function __construct(TokenParser $tokenParser)
    {
        $this->tokenParser = $tokenParser;
    }

    public function getTokenParsers()
    {
        return array($this->tokenParser);
    }

    public function getName()
    {
        return 'assetic';
    }
}
