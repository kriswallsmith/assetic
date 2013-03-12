<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2013 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Util;

use Assetic\Util\VarUtils;

class VarUtilsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getCombinationTests
     */
    public function testGetCombinations($vars, $expected)
    {
        $actual = VarUtils::getCombinations(
            $vars,
            array(
                'locale'  => array('en', 'de', 'fr'),
                'browser' => array('ie', 'firefox', 'other'),
                'gzip'    => array('gzip', '')
            )
        );

        $this->assertEquals($expected, $actual);
    }

    public function getCombinationTests()
    {
        $tests = array();

        // no variables
        $tests[] = array(
            array(),
            array(array())
        );

        // one variables
        $tests[] = array(
            array('locale'),
            array(
                array('locale' => 'en'),
                array('locale' => 'de'),
                array('locale' => 'fr'),
            )
        );

        // two variables
        $tests[] = array(
            array('locale', 'browser'),
            array(
                array('locale' => 'en', 'browser' => 'ie'),
                array('locale' => 'de', 'browser' => 'ie'),
                array('locale' => 'fr', 'browser' => 'ie'),
                array('locale' => 'en', 'browser' => 'firefox'),
                array('locale' => 'de', 'browser' => 'firefox'),
                array('locale' => 'fr', 'browser' => 'firefox'),
                array('locale' => 'en', 'browser' => 'other'),
                array('locale' => 'de', 'browser' => 'other'),
                array('locale' => 'fr', 'browser' => 'other'),
            )
        );

        // three variables
        $tests[] = array(
            array('locale', 'browser', 'gzip'),
            array(
                array('locale' => 'en', 'browser' => 'ie', 'gzip' => 'gzip'),
                array('locale' => 'de', 'browser' => 'ie', 'gzip' => 'gzip'),
                array('locale' => 'fr', 'browser' => 'ie', 'gzip' => 'gzip'),
                array('locale' => 'en', 'browser' => 'firefox', 'gzip' => 'gzip'),
                array('locale' => 'de', 'browser' => 'firefox', 'gzip' => 'gzip'),
                array('locale' => 'fr', 'browser' => 'firefox', 'gzip' => 'gzip'),
                array('locale' => 'en', 'browser' => 'other', 'gzip' => 'gzip'),
                array('locale' => 'de', 'browser' => 'other', 'gzip' => 'gzip'),
                array('locale' => 'fr', 'browser' => 'other', 'gzip' => 'gzip'),
                array('locale' => 'en', 'browser' => 'ie', 'gzip' => ''),
                array('locale' => 'de', 'browser' => 'ie', 'gzip' => ''),
                array('locale' => 'fr', 'browser' => 'ie', 'gzip' => ''),
                array('locale' => 'en', 'browser' => 'firefox', 'gzip' => ''),
                array('locale' => 'de', 'browser' => 'firefox', 'gzip' => ''),
                array('locale' => 'fr', 'browser' => 'firefox', 'gzip' => ''),
                array('locale' => 'en', 'browser' => 'other', 'gzip' => ''),
                array('locale' => 'de', 'browser' => 'other', 'gzip' => ''),
                array('locale' => 'fr', 'browser' => 'other', 'gzip' => ''),
            )
        );

        return $tests;
    }
}
