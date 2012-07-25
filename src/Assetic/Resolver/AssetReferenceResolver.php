<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Resolver;

use Assetic\AssetManager;
use Assetic\Asset\AssetReference;

/**
 * Transforms asset manager reference string into asset.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class AssetReferenceResolver implements AssetResolverInterface
{
    private $am;

    /**
     * Constructor.
     *
     * @param AssetManager $am The asset manager
     */
    public function __construct(AssetManager $am)
    {
        $this->am = $am;
    }

    /**
     * Parses an input string into an asset.
     *
     * @param string $input   An input string
     * @param array  $options An array of options
     *
     * @return AssetInterface An asset
     */
    public function resolve($input, array $options = array())
    {
        if ('@' == $input[0]) {
            return $this->createAssetReference(substr($input, 1));
        }
    }

    protected function createAssetReference($name)
    {
        if (!$this->am) {
            throw new \LogicException('There is no asset manager.');
        }

        return new AssetReference($this->am, $name);
    }
}
