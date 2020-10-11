<?php namespace Assetic\Test\Extension\Twig;

use PHPUnit\Framework\TestCase;
use Assetic\Extension\Twig\TwigResource;
use Twig\Loader\LoaderInterface;
use Twig\Loader\SourceContextLoaderInterface;
use Twig\Error\LoaderError;

class TwigResourceTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('Twig\Environment')) {
            $this->markTestSkipped('Twig is not installed.');
        }
    }

    public function testInvalidTemplateNameGetContent()
    {
        $loader = $this->prophesize(LoaderInterface::class);
        if (!method_exists(LoaderInterface::class, 'getSourceContext')) {
            $loader->willImplement(SourceContextLoaderInterface::class);
        }

        $loader->getSourceContext('asdf')->willThrow(new LoaderError(''));

        $resource = new TwigResource($loader->reveal(), 'asdf');
        $this->assertEquals('', $resource->getContent());
    }

    public function testInvalidTemplateNameIsFresh()
    {
        $loader = $this->getMockBuilder(LoaderInterface::class)->getMock();
        $loader->expects($this->once())
            ->method('isFresh')
            ->with('asdf', 1234)
            ->will($this->throwException(new LoaderError('')));

        $resource = new TwigResource($loader, 'asdf');
        $this->assertFalse($resource->isFresh(1234));
    }
}
