<?php namespace Assetic\Test\Filter;

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

    protected function setUp(): void
    {
        $tscBin = $this->findExecutable('tsc', 'TSC_BIN');
        $nodeBin = $this->findExecutable('node', 'NODE_BIN');

        if (!$tscBin) {
            $this->markTestSkipped('Unable to find `tsc` executable.');
        }

        $this->filter = new TypeScriptFilter($tscBin, $nodeBin);
    }

    protected function tearDown(): void
    {
        $this->filter = null;
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

        $this->assertStringContainsString('function greeter(person)', $asset->getContent());
        $this->assertStringNotContainsString('interface Person', $asset->getContent());
    }
}
