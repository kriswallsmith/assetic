<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\StringAsset;
use Assetic\Filter\TypeScriptFilter;

/**
 * @group integration
 */
class TypeScriptFilterTest extends FilterTestCase
{
    /**
     * @var \Assetic\Filter\TypeScriptFilter
     */
    private $filter;

    protected function setUp()
    {
        $tscBin = $this->findExecutable('tsc', 'TSC_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');

        if (!$tscBin) {
            $this->markTestSkipped('Unable to find `tsc` executable.');
        }

        $this->filter = new TypeScriptFilter($tscBin, $nodeBin);
    }

    protected function tearDown()
    {
        unset($this->filter);
    }

    public function testFilterLoad()
    {
        $typescript = <<<TYPESCRIPT
interface Person {
    firstname: string;
    lastname: string;
}

function greeter(person : Person) {
    return "Hello, " + person.firstname + " " + person.lastname;
}

var user = {firstname: "Jane", lastname: "User"};

document.body.innerHTML = greeter(user);

TYPESCRIPT;

        $asset = new StringAsset($typescript);
        $asset->load();

        $this->filter->filterLoad($asset);

        $this->assertContains('function greeter(person)', $asset->getContent());
        $this->assertNotContains('interface Person', $asset->getContent());
    }
}
