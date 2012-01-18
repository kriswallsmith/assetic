<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Filter;

use Assetic\Asset\AssetInterface;

/**
 * Runs assets through Packager, a JavaScript Compressor/Obfuscator.
 * 
 * PHP Version of the Dean Edwards's Packer, ported by Nicolas Martin.   
 *
 * @link http://joliclic.free.fr/php/javascript-packer/en/
 * @author Maximilian Walter <github@max-walter.net>
 */
class PackerFilter implements FilterInterface
{
	protected $encoding = 'None';
	
	protected $fastDecode = true;
	
	protected $specialChars = false;

	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
	}
	
	public function setFastDecode($fastDecode)
	{
		if (is_bool($fastDecode)) {
			$this->fastDecode = $fastDecode;
		}
	}
	
	public function setSpecialChars($specialChars)
	{
		if (is_bool($specialChars)) {
			$this->specialChars = $specialChars;
		}
	}

	public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $packer = new \JavaScriptPacker($asset->getContent(), $this->encoding, $this->fastDecode, $this->specialChars);
		$asset->setContent($packer->pack());
    }
}
