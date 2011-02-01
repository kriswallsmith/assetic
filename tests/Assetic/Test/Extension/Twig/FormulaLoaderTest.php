<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Twig;

use Assetic\Factory\AssetFactory;
use Assetic\Extension\Twig\AsseticExtension;
use Assetic\Extension\Twig\FormulaLoader;

class FormulaLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $am;
    private $fm;
    private $loader;

    protected function setUp()
    {
        if (!class_exists('Twig_Environment')) {
            $this->markTestSkipped('Twig is not installed.');
        }

        $this->am = $this->getMock('Assetic\\AssetManager');
        $this->fm = $this->getMock('Assetic\\FilterManager');

        $factory = new AssetFactory(__DIR__.'/templates');
        $factory->setAssetManager($this->am);
        $factory->setFilterManager($this->fm);

        $twig = new \Twig_Environment();
        $twig->setLoader(new \Twig_Loader_Filesystem(__DIR__.'/templates'));
        $twig->addExtension(new AsseticExtension($factory));

        $this->loader = new FormulaLoader($twig);
    }

    public function testMixture()
    {
        $expected = array(
            'mixture' => array(
                array('foo', 'foo/*', '@foo'),
                array(),
                'packed/mixture',
                'mixture',
                false,
            ),
        );
        
        $formulae = $this->loader->load('mixture.twig');
        $this->assertEquals($expected, $formulae);
    }
}
