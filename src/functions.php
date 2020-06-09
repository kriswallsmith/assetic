<?php

use Assetic\Factory\AssetFactory;
use Assetic\Util\TraversableString;

if (!function_exists('assetic_init')) {
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
}

if (!function_exists('assetic_javascripts')) {
    /**
     * Returns an array of javascript URLs.
     *
     * @param array|string $inputs  Input strings
     * @param array|string $filters Filter names
     * @param array        $options An array of options
     *
     * @return IteratorAggregate An array of javascript URLs
     */
    function assetic_javascripts($inputs = [], $filters = [], array $options = [])
    {
        if (!isset($options['output'])) {
            $options['output'] = 'js/*.js';
        }

        return _assetic_urls($inputs, $filters, $options);
    }
}

if (!function_exists('assetic_stylesheets')) {
    /**
     * Returns an array of stylesheet URLs.
     *
     * @param array|string $inputs Input strings
     * @param array|string $filters Filter names
     * @param array $options An array of options
     *
     * @return IteratorAggregate An array of stylesheet URLs
     */
    function assetic_stylesheets($inputs = [], $filters = [], array $options = [])
    {
        if (!isset($options['output'])) {
            $options['output'] = 'css/*.css';
        }

        return _assetic_urls($inputs, $filters, $options);
    }
}

if (!function_exists('assetic_image')) {
    /**
     * Returns an image URL.
     *
     * @param string $input An input
     * @param array|string $filters Filter names
     * @param array $options An array of options
     *
     * @return string An image URL
     */
    function assetic_image($input, $filters = [], array $options = [])
    {
        if (!isset($options['output'])) {
            $options['output'] = 'images/*';
        }

        $urls = _assetic_urls($input, $filters, $options);

        return current($urls);
    }
}

if (!function_exists('_assetic_urls')) {
    /**
     * Returns an array of asset urls.
     *
     * @param array|string $inputs Input strings
     * @param array|string $filters Filter names
     * @param array $options An array of options
     *
     * @return IteratorAggregate An array of URLs
     */
    function _assetic_urls($inputs = [], $filters = [], array $options = [])
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
        $combine = isset($options['combine']) ? $options['combine'] : !$debug;

        $one = $coll->getTargetPath();
        if ($combine) {
            $many = array($one);
        } else {
            $many = [];
            foreach ($coll as $leaf) {
                $many[] = $leaf->getTargetPath();
            }
        }

        return new TraversableString($one, $many);
    }
}
