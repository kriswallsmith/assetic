<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;

/**
 * Filters assets through CssMin.
 *
 * @link http://code.google.com/p/cssmin
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class CssMinFilter extends BaseFilter
{
    private $filters;
    private $plugins;

    public function __construct()
    {
        $this->filters = [];
        $this->plugins = [];
    }

    public function setFilters(array $filters)
    {
        $this->filters = $filters;
    }

    public function setFilter($name, $value)
    {
        $this->filters[$name] = $value;
    }

    public function setPlugins(array $plugins)
    {
        $this->plugins = $plugins;
    }

    public function setPlugin($name, $value)
    {
        $this->plugins[$name] = $value;
    }

    public function filterDump(AssetInterface $asset)
    {
        $filters = $this->filters;
        $plugins = $this->plugins;

        if (isset($filters['ImportImports']) && true === $filters['ImportImports']) {
            if ($dir = $asset->getSourceDirectory()) {
                $filters['ImportImports'] = array('BasePath' => $dir);
            } else {
                unset($filters['ImportImports']);
            }
        }

        $asset->setContent(\CssMin::minify($asset->getContent(), $filters, $plugins));
    }
}
