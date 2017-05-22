<?php

namespace voilab\cleaner\tests;

use PHPUnit\Framework\TestCase,
    voilab\cleaner\HtmlCleaner;

/**
 * @covers \voilab\cleaner\HtmlCleaner
 */
class HtmlCleanerTest extends TestCase {

    public function setUp()
    {
        $this->cleaner = new HtmlCleaner();
    }

    public function testDefaultProcessorIsStandard()
    {
        $this->assertInstanceOf(
            '\voilab\cleaner\processor\Standard',
            $this->cleaner->getProcessor()
        );
    }

    public function testChangeProcessor()
    {
        $this->cleaner->setProcessor(new processor\Mock());
        $this->assertInstanceOf(
            '\voilab\cleaner\tests\processor\Mock',
            $this->cleaner->getProcessor()
        );
    }

    public function testSetAllowedTags()
    {
        $this->cleaner->setAllowedTags(['p', 'a']);
        $this->assertEquals(['p', 'a'], $this->cleaner->getAllowedTags());
    }

    public function testResetAllowedTags()
    {
        $this->cleaner->setAllowedTags(['p', 'a']);
        $this->cleaner->setAllowedTags(['div', 'span']);
        $this->assertEquals(['div', 'span'], $this->cleaner->getAllowedTags());
    }

    public function testAddAllowedTags()
    {
        $this->cleaner->setAllowedTags(['p']);
        $this->cleaner->addAllowedTags(['a']);
        $this->assertEquals(['p', 'a'], $this->cleaner->getAllowedTags());
    }

    public function testAllowedAttributeDoesNotExist()
    {
        $this->assertEquals(false, $this->cleaner->hasAllowedAttribute('phantom'));
    }

    public function testAllowedAttributExists()
    {
        $this->cleaner->addAllowedAttributes(['class']);
        $this->assertEquals(true, $this->cleaner->hasAllowedAttribute('class'));
    }

    public function testAddAllowedAttributeAsString()
    {
        $this->cleaner->addAllowedAttributes(['class']);
        $this->assertInstanceOf(
            '\voilab\cleaner\attribute\Keep',
            $this->cleaner->getAllowedAttribute('class')
        );
    }

    public function testAddAllowedAttributeTagAsString()
    {
        $this->cleaner->addAllowedAttributes(['class:span']);
        $this->assertInstanceOf(
            '\voilab\cleaner\attribute\Keep',
            $this->cleaner->getAllowedAttribute('class', 'span')
        );
    }

    public function testGetAllowedAttribute()
    {
        $this->cleaner->setAllowedAttribute(new \voilab\cleaner\attribute\Keep('class'));
        $this->assertInstanceOf(
            '\voilab\cleaner\attribute\Keep',
            $this->cleaner->getAllowedAttribute('class')
        );
    }

    public function testRemoveAllowedAttribute()
    {
        $this->cleaner->addAllowedAttributes(['class']);
        $this->cleaner->removeAllowedAttribute('class');
        $this->assertEquals(false, $this->cleaner->hasAllowedAttribute('class'));
    }
}
