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

use Assetic\Factory\Loader\FunctionCallsFormulaLoader;

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
            ->will($this->returnValue('<?php assetic_assets('.$inputs.') ?>'));
        $factory->expects($this->once())
            ->method('generateAssetName')
            ->will($this->returnValue($name));

        $loader = new FunctionCallsFormulaLoader($factory, array(
            'assetic_assets(*)'      => array(),
            'assetic_javascripts(*)' => array('output' => 'js/*.js'),
            'assetic_stylesheets(*)' => array('output' => 'css/*.css'),
        ));
        $formulae = $loader->load($resource);

        $this->assertEquals($expected, $formulae);
    }

    public function getInputs()
    {
        return array(
            array('"js/core.js"',        'asdf', array('asdf' => array(array('js/core.js'), array(), array()))),
            array("'js/core.js'",        'asdf', array('asdf' => array(array('js/core.js'), array(), array()))),
            array("array('js/core.js')", 'asdf', array('asdf' => array(array('js/core.js'), array(), array()))),
            array('array("js/core.js")', 'asdf', array('asdf' => array(array('js/core.js'), array(), array()))),
        );
    }
}
