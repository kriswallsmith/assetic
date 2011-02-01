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

use Assetic\Factory\Factory;

class AsseticExtension extends \Twig_Extension
{
    private $factory;
    private $debug;

    public function __construct(Factory $factory, $debug = false)
    {
        $this->factory = $factory;
        $this->debug = $debug;
    }

    public function getTokenParsers()
    {
        return array(
            $this->createTokenParser($this->factory, $this->debug),
        );
    }

    public function getName()
    {
        return 'assetic';
    }

    protected function createTokenParser(Factory $factory, $debug = false)
    {
        return new TokenParser($factory, $debug);
    }
}
