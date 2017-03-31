<?php

namespace voilab\cleaner\tests\attribute;

use voilab\cleaner\attribute\Keep;

/**
 * @covers \voilab\cleaner\attribute\Keep
 */
class KeepTest extends AttributeTestCase {

    public function setUp()
    {
        $this->attribute = new Keep('class');
    }

    public function testShouldAlwaysPass()
    {
        $this->assertEquals(true, $this->attribute->pass());
    }

    public function testShouldDoNothing()
    {
        list($sxml, $dom) = $this->getElements('<p class="test">test</p>');

        $this->attribute->clean($dom);
        $this->assertEquals('<p class="test">test</p>', $sxml->asXml());
    }
}
