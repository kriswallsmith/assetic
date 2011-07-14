<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Parses assets using Twig.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class TwigFilter implements FilterInterface
{
    private $twig;
    private $context;

    public function __construct(\Twig_Environment $twig, array $context = array())
    {
        $this->twig = $twig;
        $this->context = $context;
    }

    public function addContextValue($name, $value)
    {
        $this->context[$name] = $value;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $name = 'assetic_'.substr(sha1(time().rand(11111, 99999)), 0, 7);

        eval('?>'.$this->twig->compileSource($asset->getContent(), $name));

        $class = $this->twig->getTemplateClass($name);
        $template = new $class($this->twig);

        $asset->setContent($template->render($this->context));
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
