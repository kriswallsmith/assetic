<?php

namespace Assetic\Test\Filter;

use Assetic\Filter\BaseFilter;
use Assetic\Asset\AssetInterface;

class BaseFilterTest extends \PHPUnit_Framework_TestCase
{
    public function testInterface()
    {
        $filter = new BaseFilterFilter();
        $this->assertInstanceOf('Assetic\\Filter\\FilterInterface', $filter, 'BaseFilterFilter implements FilterInterface');
    }

    private function _getTestOptionsArray()
    {
        return array(
            'test_option_camel_case' => array('test', 'asd' => 'dsa'),
            'testoptionalllowercase' => true,
        );
    }

    public function testSetOptions()
    {
        $options = $this->_getTestOptionsArray();
        $filter = new BaseFilterFilter();
        $filter->setOptions($options);
        $this->assertEquals($options['test_option_camel_case'], $filter->getTestOptionCamelCase(), 'BaseFilterFilter setOptions sets camelCase options');
        $this->assertEquals($options['testoptionalllowercase'], $filter->getTestoptionalllowercase(), 'BaseFilterFilter setOptions sets lowercase options');
    }

    public function testUnsupportedOption()
    {
        $options = array_merge(
            $this->_getTestOptionsArray(),
            array('unsupported_option' => 'test')
        );
        $filter = new BaseFilterFilter();
        $this->setExpectedException('\Assetic\Exception\FilterException', 'Assetic\Test\Filter\BaseFilterFilter::setOptions() unsupported option "unsupported_option", method "setUnsupportedOption" was not found');
        $filter->setOptions($options);
    }

    public function testNonStringOptionNameType()
    {
        $options = array_merge(
            $this->_getTestOptionsArray(),
            array(123 => 'asd')
        );
        $filter = new BaseFilterFilter();
        $this->setExpectedException('\InvalidArgumentException', 'Assetic\Test\Filter\BaseFilterFilter::setOptions() expects option name to be string, "integer" is given');
        $filter->setOptions($options);
    }

    public function testIsOptionSupported()
    {
        $filter = new BaseFilterFilter();
        $this->assertEquals(true, $filter->isOptionSupported('test_option_camel_case'));
        $this->assertEquals(true, $filter->isOptionSupported('testoptionalllowercase'));
        $this->assertEquals(false, $filter->isOptionSupported('nonexistent_option'));
    }
}

class BaseFilterFilter extends BaseFilter
{
    protected $testOptionCamelCase;

    protected $testoptionalllowercase;

    public function setTestOptionCamelCase($testOptionCamelCase)
    {
        $this->testOptionCamelCase = $testOptionCamelCase;
    }

    public function getTestOptionCamelCase()
    {
        return $this->testOptionCamelCase;
    }

    public function setTestoptionalllowercase($testoptionalllowercase)
    {
        $this->testoptionalllowercase = $testoptionalllowercase;
    }

    public function getTestoptionalllowercase()
    {
        return $this->testoptionalllowercase;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
