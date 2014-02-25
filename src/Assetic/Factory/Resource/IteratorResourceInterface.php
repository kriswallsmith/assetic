<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Resource;

/**
 * A resource is something formulae can be loaded from.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 * @author Bernhard Schussek <bschussek@gmail.com>
 *
 * @deprecated Deprecated since Assetic 1.2. Use the
 *             {@link Loader\ResourceLoaderInterface} instead.
 */
interface IteratorResourceInterface extends ResourceInterface, \IteratorAggregate
{
}
