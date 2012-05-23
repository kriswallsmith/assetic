<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Image\Processor;

use Assetic\Asset\AssetInterface;
use Assetic\Extension\Core\Processor\Context;
use Assetic\Extension\Core\Processor\ProcessorInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\Exception\ProcessFailedException;

class JpegtranProcessor implements ProcessorInterface
{
    private $options;

    public function __construct(array $options = array())
    {
        $this->options = $this->getOptionsResolver()->resolve($options);
    }

    public function process(AssetInterface $asset, Context $context)
    {
        if ($context->isAssetProcessedBy($asset, 'jpegtran') || !$content = $asset->getAttribute('content')) {
            return;
        }

        $context->markAssetProcessedBy($asset, 'jpegtran');

        if (!$this->options['binary']) {
            $this->options['binary'] = $context->findExecutable('jpegtran');
            if (!$this->options['binary']) {
                throw new \RuntimeException('You must provide a "binary" option');
            }
        }

        $pb = $context->createProcessBuilder(array($this->options['binary']));

        if ($this->options['copy']) {
            $pb->add('-copy')->add($this->options['copy']);
        }

        if ($this->options['optimize']) {
            $pb->add('-optimize');
        }

        if ($this->options['progressive']) {
            $pb->add('-progressive');
        }

        if ($this->options['restart']) {
            $pb->add('-restart')->add($this->options['restart']);
        }

        $file = $context->createTempFile('jpegtran', $path);
        $file->setContent($content);
        $pb->add($path);

        $proc = $pb->getProcess();
        $proc->run();

        $file->delete();

        if (!$proc->isSuccessful()) {
            throw new ProcessFailedException($proc);
        }

        $asset->setAttribute('content', $proc->getOutput());
    }

    protected function getOptionsResolver()
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults(array(
            'binary'      => null,
            'copy'        => null,
            'optimize'    => null,
            'progressive' => null,
            'restart'     => null,
        ));

        $resolver->setAllowedValues(array(
            'copy'        => array(null, 'none', 'comments', 'all'),
            'optimize'    => array(null, true, false),
            'progressive' => array(null, true, false),
        ));

        return $resolver;
    }
}
