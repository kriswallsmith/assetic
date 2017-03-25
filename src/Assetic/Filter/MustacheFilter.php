<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2011 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Filters assets through CssMin.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class MustacheFilter implements FilterInterface
{

    protected $tplExtension;

    protected $viewsNamespace;

    protected $rootPath;

    public function __construct($tplExtension = '.js.hmtl', $viewsNamespace = 'views', $rootPath = '')
    {
        $this->tplExtension = $tplExtension;
        $this->viewsNamespace = $viewsNamespace;
        $this->rootPath = $rootPath;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $assetPath = $asset->getSourcePath();
        if (0 == preg_match('/.*' . preg_quote($this->tplExtension) . '$/', $assetPath)) {
            $asset->setContent('');
            return;
        }

        $pattern = '^\/?' . preg_quote($this->rootPath, '/')  . '\/?(.*)' .
                preg_quote($this->tplExtension, '/') . '$';
        $assetPath = preg_replace("/{$pattern}/", '$1', $assetPath);
        $assetPath = str_replace('/', '_', $assetPath);

        $asset->setContent($this->viewsNamespace . '.' . $assetPath . ' = ' . json_encode($asset->getContent()) . ";\n");
    }
}
