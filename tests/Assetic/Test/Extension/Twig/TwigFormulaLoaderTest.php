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
use Assetic\Extension\Twig\TwigFormulaLoader;

class TwigFormulaLoaderTest extends \PHPUnit_Framework_TestCase
{
    private $am;
    private $fm;
    private $twig;

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

        $this->twig = new \Twig_Environment(new \Twig_Loader_Filesystem(__DIR__.'/templates'));
        $this->twig->addExtension(new AsseticExtension($factory));
    }

    public function testMixture()
    {
        $expected = array(
            'mixture' => array(
                array('foo', 'foo/*', '@foo'),
                array(),
                array(
                    'output' => 'packed/mixture',
                    'name'   => 'mixture',
                    'debug'  => false,
                ),
            ),
        );

        $loader = new TwigFormulaLoader($this->twig, 'mixture.twig');
        $formulae = $loader->load();

        $this->assertEquals($expected, $formulae);
    }
}
