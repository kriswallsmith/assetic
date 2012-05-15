<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic;

use Assetic\Asset\Factory;
use Assetic\Asset\FactoryInterface;
use Assetic\Tree\Comparator;
use Assetic\Tree\RepeatingTraverser;
use Assetic\Tree\Traverser;
use Assetic\Tree\TraverserInterface;

class Environment implements EnvironmentInterface
{
    private $initialized;
    private $extensions;
    private $factory;
    private $loader;
    private $processor;

    public function __construct(FactoryInterface $factory = null)
    {
        $this->initialized = false;
        $this->extensions = array();
        $this->factory = $factory ?: new Factory();
    }

    public function addExtension(ExtensionInterface $extension)
    {
        if ($this->initialized) {
            throw new \LogicException('Cannot add an extension after environment is initialized');
        }

        $this->extensions[] = $extension;
    }

    public function getExtensions()
    {
        return $this->extensions;
    }

    public function initialize()
    {
        $this->loader = new RepeatingTraverser(new Traverser(), new Comparator());
        $this->processor = new Traverser();
        $this->initialized = true;

        // initialize each extension
        foreach ($this->extensions as $extension) {
            $extension->initialize($this);
        }

        // add visitors
        foreach ($this->extensions as $extension) {
            foreach ($extension->getLoaderVisitors() as $visitor) {
                $this->loader->addVisitor($visitor);
            }

            foreach ($extension->getProcessorVisitors() as $visitor) {
                $this->processor->addVisitor($visitor);
            }
        }
    }

    public function getLoader()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return $this->loader;
    }

    public function getProcessor()
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return $this->processor;
    }
}
