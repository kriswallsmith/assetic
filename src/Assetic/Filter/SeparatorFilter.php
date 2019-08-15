<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;

/**
 * Inserts a separator between assets to prevent merge failures
 * e.g. missing semicolon at the end of a JS file
 *
 * @author Robin McCorkell <rmccorkell@karoshi.org.uk>
 */
class SeparatorFilter extends BaseFilter
{
    /**
     * @var string
     */
    private $separator;

    /**
     * Constructor.
     *
     * @param string $separator Separator to use between assets
     */
    public function __construct($separator = ';')
    {
        $this->separator = $separator;
    }

    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent($asset->getContent() . $this->separator);
    }
}
