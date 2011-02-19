<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Loader;

use Assetic\Factory\Resource\ResourceInterface;

/**
 * An aggregation of many formula loaders
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AggregateFormulaLoader implements FormulaLoaderInterface
{
    private $loaders = array();

    public function __construct(array $loaders)
    {
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    public function addLoader(FormulaLoaderInterface $loader)
    {
        $this->loaders[] = $loader;
    }

    public function supports(ResourceInterface $resource)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($resource)) {
                return true;
            }
        }

        return false;
    }

    public function load(ResourceInterface $resource)
    {
        foreach ($this->loaders as $loader) {
            if ($loader->supports($resource)) {
                return $loader->load($resource);
            }
        }

        throw new \InvalidArgumentException('There is no loader for the supplied resource.');
    }
}
