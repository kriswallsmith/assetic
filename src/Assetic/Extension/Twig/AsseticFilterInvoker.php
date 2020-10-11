<?php namespace Assetic\Extension\Twig;

/**
 * Filters a single asset.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class AsseticFilterInvoker
{
    private $factory;
    private $filters;
    private $options;

    public function __construct($factory, $filter)
    {
        $this->factory = $factory;

        if (is_array($filter) && isset($filter['filter'])) {
            $this->filters = (array) $filter['filter'];
            $this->options = isset($filter['options']) ? (array) $filter['options'] : [];
        } else {
            $this->filters = (array) $filter;
            $this->options = [];
        }
    }

    public function getFactory()
    {
        return $this->factory;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function invoke($input, array $options = [])
    {
        $asset = $this->factory->createAsset($input, $this->filters, $options + $this->options);

        return $asset->getTargetPath();
    }
}
