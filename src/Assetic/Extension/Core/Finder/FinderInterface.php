<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Extension\Core\Finder;

/**
 * A finder takes a path and returns a source.
 */
interface FinderInterface
{
    /**
     * Loads a source by logical path.
     *
     * @param string $logicalPath A logical path
     *
     * @return SourceInterface The source
     */
    function findByLogicalPath($logicalPath);
}
