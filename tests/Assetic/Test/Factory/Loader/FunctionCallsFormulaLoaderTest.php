<?php namespace Assetic\Test\Factory\Loader;

use PHPUnit\Framework\TestCase;
use Assetic\Contracts\Factory\Resource\ResourceInterface;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\Loader\FunctionCallsFormulaLoader;
use Assetic\Factory\Resource\FileResource;

class FunctionCallsFormulaLoaderTest extends TestCase
{
    /**
     * @dataProvider getJavascriptInputs
     */
    public function testInput($function, $inputs, $name, $expected)
    {
        $resource = $this->getMockBuilder(ResourceInterface::class)->getMock();
        $factory = $this->getMockBuilder(AssetFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resource->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('<?php '.$function.'('.$inputs.') ?>'));
        $factory->expects($this->once())
            ->method('generateAssetName')
            ->will($this->returnValue($name));

        $loader = new FunctionCallsFormulaLoader($factory);
        $formulae = $loader->load($resource);

        $this->assertEquals($expected, $formulae);
    }

    public function getJavascriptInputs()
    {
        return array(
            array('assetic_javascripts', '"js/core.js"',        'asdf', array('asdf' => array(array('js/core.js'), [], array('debug' => false, 'output' => 'js/*.js', 'name' => 'asdf')))),
            array('assetic_javascripts', "'js/core.js'",        'asdf', array('asdf' => array(array('js/core.js'), [], array('debug' => false, 'output' => 'js/*.js', 'name' => 'asdf')))),
            array('assetic_javascripts', "array('js/core.js')", 'asdf', array('asdf' => array(array('js/core.js'), [], array('debug' => false, 'output' => 'js/*.js', 'name' => 'asdf')))),
            array('assetic_javascripts', 'array("js/core.js")', 'asdf', array('asdf' => array(array('js/core.js'), [], array('debug' => false, 'output' => 'js/*.js', 'name' => 'asdf')))),
            array('assetic_image',       '"images/logo.gif"',   'asdf', array('asdf' => array(array('images/logo.gif'), [], array('debug' => false, 'output' => 'images/*', 'name' => 'asdf')))),
        );
    }

    public function testComplexFormula()
    {
        $factory  = new AssetFactory(__DIR__.'/templates', true);
        $loader   = new FunctionCallsFormulaLoader($factory);
        $resource = new FileResource(__DIR__.'/templates/debug.php');
        $formulae = $loader->load($resource);

        $this->assertEquals(array(
            'test123' => array(
                array('foo.css', 'bar.css'),
                array('?foo', 'bar'),
                array('name' => 'test123', 'output' => 'css/packed.css', 'debug' => true),
            ),
        ), $formulae);
    }
}
