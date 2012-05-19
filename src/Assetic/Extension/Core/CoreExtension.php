<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Core;

use Assetic\AbstractExtension;
use Assetic\Extension\Core\Finder\ChainFinder;
use Assetic\Extension\Core\Finder\FileFinder;
use Assetic\Extension\Core\Processor\AggregateProcessor;
use Assetic\Extension\Core\Processor\ExtensionProcessor;
use Assetic\Extension\Core\Processor\MimeTypeProcessor;
use Assetic\Extension\Core\Processor\ProcessorInterface;
use Assetic\Extension\Core\Visitor\LogicalPathVisitor;
use Assetic\Extension\Core\Visitor\ProcessorVisitor;
use Assetic\Extension\Core\Visitor\SourceVisitor;

/**
 * Introduces the concepts of finders, sources, and processors.
 */
class CoreExtension extends AbstractExtension
{
    const PRIORITY_PRE     = 10;
    const PRIORITY_DEFAULT = 0;
    const PRIORITY_POST    = -10;

    private $basePaths;
    private $finders;
    private $processors;

    public function __construct(array $basePaths = array())
    {
        $this->basePaths = $basePaths;
        $this->finders = array();
        $this->processors = array();
    }

    public function getLoaderVisitors()
    {
        $visitors = array(
            new SourceVisitor($this->getFinder()),
            new LogicalPathVisitor(),
        );

        if ($processor = $this->getProcessor()) {
            $visitors[] = new ProcessorVisitor($processor);
        }

        return $visitors;
    }

    public function registerFinder(FinderInterface $finder)
    {
        $this->finders[] = $finder;

        return $this;
    }

    public function registerPreProcessor(ProcessorInterface $processor, $mimeType = null, $priority = self::PRIORITY_PRE)
    {
        if ($mimeType) {
            $processor = new MimeTypeProcessor($mimeType, $processor);
        }

        $this->processors[$priority][] = $processor;

        return $this;
    }

    public function registerProcessor(ProcessorInterface $processor, $extension = null, $priority = self::PRIORITY_DEFAULT)
    {
        if ($extension) {
            $processor = new ExtensionProcessor($mimeType, $processor);
        }

        $this->processors[$priority][] = $processor;

        return $this;
    }

    public function registerPostProcessor(ProcessorInterface $processor, $mimeType = null, $priority = self::PRIORITY_POST)
    {
        if ($mimeType) {
            $processor = new MimeTypeProcessor($mimeType, $processor);
        }

        $this->processors[$priority][] = $processor;

        return $this;
    }

    public function getName()
    {
        return 'core';
    }

    // private

    private function getFinder()
    {
        $finders = $this->finders;
        $finders[] = new FileFinder($this->basePaths);

        return 1 == count($finders) ? $finders[0] : new ChainFinder($finders);
    }

    private function getProcessor()
    {
        if (!$this->processors) {
            return;
        }

        krsort($this->processors);
        $processors = call_user_func_array('array_merge', $this->processors);

        return 1 == count($processors) ? $processors[0] : new AggregateProcessor($processors);
    }
}
