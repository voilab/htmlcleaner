<?php

namespace voilab\cleaner\tests\attribute;

use voilab\cleaner\attribute\Remove;

/**
 * @covers \voilab\cleaner\attribute\Remove
 */
class RemoveTest extends AttributeTestCase {

    public function setUp()
    {
        $this->attribute = new Remove('class');
    }

    public function testShouldAlwaysStop()
    {
        $this->assertEquals(false, $this->attribute->pass());
    }

    public function testRemoveAttribute()
    {
        list($sxml, $dom) = $this->getElements('<p class="test">test</p>');

        $this->attribute->clean($dom);
        $this->assertEquals('<p>test</p>', $sxml->asXml());
    }
}
