<?php namespace Assetic;

use Assetic\Contracts\Filter\FilterInterface;

/**
 * Manages the available filters.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class FilterManager
{
    private $filters = [];

    public function set($alias, FilterInterface $filter)
    {
        $this->checkName($alias);

        $this->filters[$alias] = $filter;
    }

    public function get($alias)
    {
        if (!isset($this->filters[$alias])) {
            throw new \InvalidArgumentException(sprintf('There is no "%s" filter.', $alias));
        }

        return $this->filters[$alias];
    }

    public function has($alias)
    {
        return isset($this->filters[$alias]);
    }

    public function getNames()
    {
        return array_keys($this->filters);
    }

    /**
     * Checks that a name is valid.
     *
     * @param string $name An asset name candidate
     *
     * @throws \InvalidArgumentException If the asset name is invalid
     */
    protected function checkName($name)
    {
        if (!ctype_alnum(str_replace('_', '', $name))) {
            throw new \InvalidArgumentException(sprintf('The name "%s" is invalid.', $name));
        }
    }
}
