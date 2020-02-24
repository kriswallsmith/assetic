<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Asset;

use Assetic\Filter\FilterInterface;
use Assetic\Util\VarUtils;

/**
 * Represents an asset loaded via an HTTP request using a proxy
 *
 * @author Bianka Martinovic <blackbird@webbird.de>
 */
class HttpAssetWithProxy extends HttpAsset
{
    public function __construct($sourceUrl, $filters = array(), $ignoreErrors = false, array $vars = array(), $proxy, $port)
    {
        parent::__construct($sourceUrl,$filters,$ignoreErrors,$vars);
        $this->vars['proxy'] = $proxy;
        $this->vars['proxy_port'] = $port;
    }
}
