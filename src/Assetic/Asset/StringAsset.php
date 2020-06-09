<?php namespace Assetic\Asset;

use Assetic\Contracts\Filter\FilterInterface;

/**
 * Represents a string asset.
 *
 * @author Kris Wallsmith <kris.wallsmith@gmail.com>
 */
class StringAsset extends BaseAsset
{
    private $string;
    private $lastModified;

    /**
     * Constructor.
     *
     * @param string $content    The content of the asset
     * @param array  $filters    Filters for the asset
     * @param string $sourceRoot The source asset root directory
     * @param string $sourcePath The source asset path
     */
    public function __construct($content, $filters = [], $sourceRoot = null, $sourcePath = null)
    {
        $this->string = $content;

        parent::__construct($filters, $sourceRoot, $sourcePath);
    }

    public function load(FilterInterface $additionalFilter = null)
    {
        $this->doLoad($this->string, $additionalFilter);
    }

    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }

    public function getLastModified()
    {
        return $this->lastModified;
    }
}
