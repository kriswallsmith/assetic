<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Extension\Twig;

use Assetic\Extension\Twig\AsseticExtension;

class LazyAsseticExtensionTest extends AsseticExtensionTest
{
    protected function setUp()
    {
        parent::setUp();

        $this->twig = new RandomizedTwigEnvironment();
        $this->twig->setLoader(new \Twig_Loader_Filesystem(__DIR__.'/templates'));
        $this->twig->addExtension(new AsseticExtension($this->factory, array(), $this->valueSupplier, true));
    }
}
