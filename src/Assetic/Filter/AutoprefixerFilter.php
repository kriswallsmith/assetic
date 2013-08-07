<?php namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Exception\FilterException;
use Assetic\Filter\BaseNodeFilter;

/**
 * Autoprefixer filter.
 */
class AutoprefixerFilter extends BaseNodeFilter
{
	private $autoprefixerBin;
	private $nodeBin;

	/**
	 * @param string $autoprefixerBin Absolute path to the autoprefixer executable
	 * @param string $nodeBin      Absolute path to the folder containg node.js executable
	 */
	public function __construct($autoprefixerBin = '/usr/bin/autoprefixer', $nodeBin = null)
	{
		$this->autoprefixerBin = $autoprefixerBin;
		$this->nodeBin = $nodeBin;
	}

	/**
	 * @see Assetic\Filter\FilterInterface::filterLoad()
	 */
	public function filterLoad(AssetInterface $asset)
	{
	}

	/**
	 * Run the asset through Autoprefixer
	 *
	 * @see Assetic\Filter\FilterInterface::filterDump()
	 */
	public function filterDump(AssetInterface $asset)
	{
		$pb = $this->createProcessBuilder($this->nodeBin
			? array($this->nodeBin, $this->autoprefixerBin)
			: array($this->autoprefixerBin));

		// input and output files
		$input = tempnam(sys_get_temp_dir(), 'input');
		$output = tempnam(sys_get_temp_dir(), 'output');

		file_put_contents($input, $asset->getContent());
		$pb->add($input)->add('-o')->add($output);

		$proc = $pb->getProcess();
		$code = $proc->run();
		unlink($input);

		if (0 !== $code) {
			if (file_exists($output)) {
				unlink($output);
			}

			if (127 === $code) {
				throw new \RuntimeException('Path to node executable could not be resolved.');
			}

			throw FilterException::fromProcess($proc)->setInput($asset->getContent());
		}

		if (!file_exists($output)) {
			throw new \RuntimeException('Error creating output file.');
		}

		$autoprefixedCSS = file_get_contents($output);
		unlink($output);

		$asset->setContent($autoprefixedCSS);
	}
}
