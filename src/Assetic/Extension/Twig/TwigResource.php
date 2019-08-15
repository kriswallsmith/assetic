<?php namespace Assetic\Extension\Twig;

use Assetic\Contracts\Factory\Resource\ResourceInterface;
use Twig\Loader\LoaderInterface;
use Twig\Error\LoaderError;

/**
 * A Twig template resource.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class TwigResource implements ResourceInterface
{
    private $loader;
    private $name;

    public function __construct(LoaderInterface $loader, $name)
    {
        $this->loader = $loader;
        $this->name = $name;
    }

    public function getContent()
    {
        try {
            return method_exists($this->loader, 'getSourceContext')
                ? $this->loader->getSourceContext($this->name)->getCode()
                : $this->loader->getSource($this->name);
        } catch (LoaderError $e) {
            return '';
        }
    }

    public function isFresh($timestamp)
    {
        try {
            return $this->loader->isFresh($this->name, $timestamp);
        } catch (LoaderError $e) {
            return false;
        }
    }

    public function __toString()
    {
        return $this->name;
    }
}
