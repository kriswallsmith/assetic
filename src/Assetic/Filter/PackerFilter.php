<?php namespace Assetic\Filter;

use Assetic\Contracts\Asset\AssetInterface;

/**
 * Runs assets through Packager, a JavaScript Compressor/Obfuscator.
 *
 * PHP Version of the Dean Edwards's Packer, ported by Nicolas Martin.
 *
 * @link http://joliclic.free.fr/php/javascript-packer/en/
 * @author Maximilian Walter <github@max-walter.net>
 */
class PackerFilter extends BaseFilter
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
        $this->fastDecode = (bool) $fastDecode;
    }

    public function setSpecialChars($specialChars)
    {
        $this->specialChars = (bool) $specialChars;
    }

    public function filterDump(AssetInterface $asset)
    {
        $packer = new \JavaScriptPacker($asset->getContent(), $this->encoding, $this->fastDecode, $this->specialChars);
        $asset->setContent($packer->pack());
    }
}
