<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Assetic\Asset\UrlCollection;
use Assetic\Factory\AssetFactory;

/**
 * Initializes the global Assetic object.
 *
 * @param AssetFactory $factory The asset factory
 */
function assetic_init(AssetFactory $factory)
{
    global $_assetic;

    $_assetic = new stdClass();
    $_assetic->factory = $factory;
}

/**
 * Returns an array of javascript URLs.
 *
 * @param array|string $inputs  Input strings
 * @param array|string $filters Filter names
 * @param array        $options An array of options
 *
 * @return array An array of javascript URLs
 */
function assetic_javascripts($inputs = array(), $filters = array(), array $options = array())
{
    global $_assetic;

    if (!isset($options['output'])) {
        $options['output'] = 'js/*.js';
    }

    return _assetic_assets($inputs, $filters, $options);
}

/**
 * Returns an array of stylesheet URLs.
 *
 * @param array|string $inputs  Input strings
 * @param array|string $filters Filter names
 * @param array        $options An array of options
 *
 * @return array An array of stylesheet URLs
 */
function assetic_stylesheets($inputs = array(), $filters = array(), array $options = array())
{
    global $_assetic;

    if (!isset($options['output'])) {
        $options['output'] = 'css/*.css';
    }

    return _assetic_assets($inputs, $filters, $options);
}

/**
 * Returns an image URL.
 *
 * @param string       $input   An input
 * @param array|string $filters Filter names
 * @param array        $options An array of options
 *
 * @return string An image URL
 */
function assetic_image($input, $filters = array(), array $options = array())
{
    global $_assetic;

    if (!isset($options['output'])) {
        $options['output'] = 'images/*';
    }

    $urls = _assetic_assets($input, $filters, $options);

    return current($urls);
}

/**
 * Returns an array of asset urls.
 *
 * @param array|string $inputs  Input strings
 * @param array|string $filters Filter names
 * @param array        $options An array of options
 *
 * @return array An array of URLs
 */
function _assetic_assets($inputs = array(), $filters = array(), array $options = array())
{
    global $_assetic;

    if (!is_array($inputs)) {
        $inputs = array_filter(array_map('trim', explode(',', $inputs)));
    }

    if (!is_array($filters)) {
        $filters = array_filter(array_map('trim', explode(',', $filters)));
    }

    $coll = $_assetic->factory->createAsset($inputs, $filters, $options);
    $debug = isset($options['debug']) ? $options['debug'] : $_assetic->factory->isDebug();

    return UrlCollection::createFromAssetCollection($coll, $debug);
}
