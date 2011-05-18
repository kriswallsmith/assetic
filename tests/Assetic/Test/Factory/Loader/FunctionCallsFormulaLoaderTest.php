<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Factory\Loader;

use Assetic\Factory\AssetFactory;
use Assetic\Factory\Loader\FunctionCallsFormulaLoader;
use Assetic\Factory\Resource\FileResource;

class FunctionCallsFormulaLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getInputs
     */
    public function testInput($inputs, $name, $expected)
    {
        $resource = $this->getMock('Assetic\\Factory\\Resource\\ResourceInterface');
        $factory = $this->getMockBuilder('Assetic\\Factory\\AssetFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $resource->expects($this->once())
            ->method('getContent')
            ->will($this->returnValue('<?php assetic_javascripts('.$inputs.') ?>'));
        $factory->expects($this->once())
            ->method('generateAssetName')
            ->will($this->returnValue($name));

        $loader = new FunctionCallsFormulaLoader($factory);
        $formulae = $loader->load($resource);

        $this->assertEquals($expected, $formulae);
    }

    public function getInputs()
    {
        return array(
            array('"js/core.js"',        'asdf', array('asdf' => array(array('js/core.js'), array(), array('debug' => false, 'output' => 'js/*.js', 'name' => 'asdf', )))),
            array("'js/core.js'",        'asdf', array('asdf' => array(array('js/core.js'), array(), array('debug' => false, 'output' => 'js/*.js', 'name' => 'asdf', )))),
            array("array('js/core.js')", 'asdf', array('asdf' => array(array('js/core.js'), array(), array('debug' => false, 'output' => 'js/*.js', 'name' => 'asdf', )))),
            array('array("js/core.js")', 'asdf', array('asdf' => array(array('js/core.js'), array(), array('debug' => false, 'output' => 'js/*.js', 'name' => 'asdf', )))),
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
