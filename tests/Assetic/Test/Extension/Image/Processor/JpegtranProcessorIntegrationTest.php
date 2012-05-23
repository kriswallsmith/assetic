<?php

/*
 * This file is part of Assetic, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Image\Processor;

use Assetic\Asset\Asset;
use Assetic\Extension\Core\Processor\Context;
use Assetic\Extension\Image\Processor\JpegtranProcessor;
use Symfony\Component\Process\ExecutableFinder;

/**
 * @group integration
 */
class JpegtranProcessorIntegrationTest extends \PHPUnit_Framework_TestCase
{
    private $context;
    private $processor;

    protected function setUp()
    {
        $this->context = new Context();
        if (!$binary = $this->context->findExecutable('jpegtran')) {
            $this->markTestSkipped('jpegtran is not installed');
        }

        $this->processor = new JpegtranProcessor(array('binary' => $binary));
    }

    /**
     * @test
     * @dataProvider provideOptions
     */
    public function shouldActuallyWork($options)
    {
        $asset = new Asset(array(
            'content' => $raw = file_get_contents(__DIR__.'/Fixtures/white.jpg'),
        ));

        $this->processor->process($asset, $this->context, $options);

        $optimized = $asset->getAttribute('content');

        $this->assertNotEmpty($optimized);
        $this->assertNotEquals($raw, $optimized);
    }

    public function provideOptions()
    {
        return array(
            array(array('copy' => 'none', 'optimize' => true)),
            array(array('copy' => 'none', 'progressive' => true)),
        );
    }

    /**
     * @test
     * @expectedException Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function shouldErrorOnInvalidImage()
    {
        $asset = new Asset(array('content' => file_get_contents(__DIR__.'/Fixtures/clear.gif')));

        $this->processor->process($asset, $this->context);
    }
}
