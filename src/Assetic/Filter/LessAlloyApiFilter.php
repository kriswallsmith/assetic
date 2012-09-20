<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Loads LESS files using the Alloy API webservice.
 *
 * Less files should be identical to the regular LessFilter sans the node dep.
 *
 * @link http://divshot.com/alloy
 *
 * @author Chris Christensen <christianchristensen@gmail.com>
 */
class LessAlloyApiFilter implements FilterInterface
{
    public function filterLoad(AssetInterface $asset)
    {
        // @TODO Push this API boilerplate to an Alloy API baseclass to reuse
        //  for SASS, Sylus, etc...
        $query = array(
            'source'   => $asset->getContent(),
            'type'     => 'less',
            //'compress' => 'true',
        );

        if (preg_match('/1|yes|on|true/i', ini_get('allow_url_fopen'))) {
            $context = stream_context_create(array('http' => array(
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-encoded',
                'content' => http_build_query($query),
            )));

            $response = file_get_contents('http://alloy.divshot.com/compile', false, $context);
        } elseif (defined('CURLOPT_POST') && !in_array('curl_init', explode(',', ini_get('disable_functions')))) {
            $ch = curl_init('http://alloy.divshot.com/compile');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-encoded'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            $response = curl_exec($ch);
            curl_close($ch);
        } else {
            throw new \RuntimeException("There is no known way to contact Alloy API available");
        }

        $asset->setContent($response);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
