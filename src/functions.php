<?php

/*
 * This file is part of the Assetic package.
 *
 * (c) Kris Wallsmith <kris.wallsmith@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Assetic\Factory\AssetFactory;

/**
 * Initializes the global Assetic object.
 *
 * Available options:
 *
 *  * default_css_output: Default output option for assetic_stylesheets()
 *  * default_js_output:  Default output option for assetic_javascripts()
 *
 * @param AssetFactory $factory The asset factory
 * @param array        $options An array of options
 */
function assetic_init(AssetFactory $factory, array $options = array())
{
    global $_assetic;

    $_assetic = new stdClass();
    $_assetic->factory = $factory;
    $_assetic->options = $options;
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
function assetic_assets($inputs = array(), $filters = array(), array $options = array())
{
    global $_assetic;

    if (!is_array($inputs)) {
        $inputs = array_filter(array_map('trim', explode(',', $inputs)));
    }

    if (!is_array($filters)) {
        $filters = array_filter(array_map('trim', explode(',', $filters)));
    }

    $coll = $_assetic->factory->createAsset($inputs, $filters, $options);
    if (!$_assetic->factory->isDebug()) {
        return array($coll->getTargetUrl());
    }

    $urls = array();
    foreach ($coll as $leaf) {
        $urls[] = $leaf->getTargetUrl();
    }

    return $urls;
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
        $options['output'] = isset($_assetic->options['default_js_output']) ? $_assetic->options['default_js_output'] : 'js/*.js';
    }

    return assetic_assets($inputs, $filters, $options);
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
        $options['output'] = isset($_assetic->options['default_css_output']) ? $_assetic->options['default_css_output'] : 'css/*.css';
    }

    return assetic_assets($inputs, $filters, $options);
}
