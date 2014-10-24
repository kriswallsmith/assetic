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

use Assetic\Asset\FileAsset;
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

    private $tscBin;

    private $nodeBin;

    protected function setUp()
    {
        $this->tscBin = $this->findExecutable('tsc', 'TSC_BIN');
        $this->nodeBin = $this->findExecutable('node', 'NODE_BIN');

        if (!$this->tscBin) {
            $this->markTestSkipped('Unable to find `tsc` executable.');
        }

        $this->filter = new TypeScriptFilter($this->tscBin, $this->nodeBin);
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

    public function testRelativeToAbsolutepaths()
    {
        $tmpDir = sys_get_temp_dir();
        $tmpName = uniqid('php_assetic_test') . '.ts';
        $tmpFile = $tmpDir.DIRECTORY_SEPARATOR.$tmpName;
        file_put_contents($tmpFile, '');

        $typescript = <<<TYPESCRIPT
/// <reference path="{$tmpName}" />
/// <reference path="{$tmpName}"         />
///<reference path="{$tmpName}" />
///<reference path="{$tmpName}"/>
///<reference      path="{$tmpName}" />
     /// <reference path="{$tmpName}" />

TYPESCRIPT;

        $expectedOutput = <<<TYPESCRIPT
/// <reference path="{$tmpFile}" />
/// <reference path="{$tmpFile}"         />
///<reference path="{$tmpFile}" />
///<reference path="{$tmpFile}"/>
///<reference      path="{$tmpFile}" />
/// <reference path="{$tmpFile}" />

TYPESCRIPT;

        $asset = new StringAsset($typescript, [], $tmpDir, 'test');
        $asset->load();
        $filter = new TypeScriptFilter($this->tscBin, $this->nodeBin, ['use_real_path' => true]);
        $filter->filterLoad($asset);

        unlink($tmpFile);

        $this->assertEquals($expectedOutput, $asset->getContent());
    }
}
