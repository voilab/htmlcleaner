<?php

namespace voilab\cleaner\tests\attribute;

use voilab\cleaner\attribute\Js;

/**
 * @covers \voilab\cleaner\attribute\Js
 */
class JsTest extends AttributeTestCase {

    public function setUp()
    {
        $this->attribute = new Js('href');
    }

    public function testStopIfJavascript()
    {
        list($sxml, $dom) = $this->prepare('<a href="javascript:alert(\'test\')">test</a>');
        $this->assertEquals(false, $this->attribute->pass());
    }

    public function testPassIfGoodUrl()
    {
        list($sxml, $dom) = $this->prepare('<a href="somesite.org">test</a>');
        $this->assertEquals(true, $this->attribute->pass());
    }

    public function testChangeAttribute()
    {
        list($sxml, $dom) = $this->prepare('<a href="javascript:alert(\'test\')">test</a>');
        $this->attribute->clean($dom);
        $this->assertEquals('<a href="#">test</a>', $sxml->asXml());
    }

    private function prepare($html)
    {
        list($sxml, $dom) = $this->getElements($html);
        $this->attribute->setAttribute($this->getAttribute($sxml, 'href'));
        return [$sxml, $dom];
    }
}
