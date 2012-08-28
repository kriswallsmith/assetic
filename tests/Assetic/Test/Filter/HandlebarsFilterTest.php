<?php

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2012 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Assetic\Test\Filter;

use Assetic\Asset\FileAsset;
use Assetic\Filter\HandlebarsFilter;

/**
 * @group integration
 */
class HandlebarsFilterTest extends \PHPUnit_Framework_TestCase
{
    private $asset;
    private $filter;

    protected function setUp()
    {
        if (!isset($_SERVER['HANDLEBARS_BIN'])) {
            $this->markTestSkipped('There is no handlebars configuration.');
        }

        $this->asset = new FileAsset(__DIR__.'/fixtures/handlebars/template.handlebars');
        $this->asset->load();

        if (isset($_SERVER['NODE_BIN'])) {
            $this->filter = new HandlebarsFilter($_SERVER['HANDLEBARS_BIN'], $_SERVER['NODE_BIN']);
        } else {
            $this->filter = new HandlebarsFilter($_SERVER['HANDLEBARS_BIN']);
        }
    }

    protected function tearDown()
    {
        $this->asset = null;
        $this->filter = null;
    }

    public function testHandlebars()
    {
        $this->filter->filterLoad($this->asset);

        $expected = <<<JS
(function() {
  var template = Handlebars.template, templates = Handlebars.templates = Handlebars.templates || {};
templates['template'] = template(function (Handlebars,depth0,helpers,partials,data) {
  helpers = helpers || Handlebars.helpers;
  var buffer = "", stack1, foundHelper, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<div id=\"test\"><h2>";
  foundHelper = helpers['var'];
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0['var']; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "</h2></div>";
  return buffer;});
})();
JS;
        $this->assertSame($expected, $this->asset->getContent());
    }

    public function testSimpleHandlebars()
    {
        $this->filter->setSimple(true);
        $this->filter->filterLoad($this->asset);

        $expected = <<<JS
function (Handlebars,depth0,helpers,partials,data) {
  helpers = helpers || Handlebars.helpers;
  var buffer = "", stack1, foundHelper, functionType="function", escapeExpression=this.escapeExpression;


  buffer += "<div id=\"test\"><h2>";
  foundHelper = helpers['var'];
  if (foundHelper) { stack1 = foundHelper.call(depth0, {hash:{}}); }
  else { stack1 = depth0['var']; stack1 = typeof stack1 === functionType ? stack1() : stack1; }
  buffer += escapeExpression(stack1) + "</h2></div>";
  return buffer;}

JS;
        $this->assertSame($expected, $this->asset->getContent());
    }

    public function testMinimizeHandlebars()
    {
        $this->filter->setMinimize(true);
        $this->filter->filterLoad($this->asset);

        $expected = <<<JS
(function(){var a=Handlebars.template,b=Handlebars.templates=Handlebars.templates||{};b.template=a(function(a,b,c,d,e){c=c||a.helpers;var f="",g,h,i="function",j=this.escapeExpression;return f+='<div id="test"><h2>',h=c["var"],h?g=h.call(b,{hash:{}}):(g=b["var"],g=typeof g===i?g():g),f+=j(g)+"</h2></div>",f})})()
JS;
        $this->assertSame($expected, $this->asset->getContent());
    }
}
