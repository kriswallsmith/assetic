<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Factory\Resource;

/**
 * A file resource.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
interface FileResourceInterface extends ResourceInterface
{
    /**
     * Returns the path to the file.
     *
     * @return string An absolute filesystem path
     */
    function getPath();
}
