<?php

namespace voilab\cleaner\tests\processor;

use PHPUnit\Framework\TestCase,
    voilab\cleaner\HtmlCleaner,
    voilab\cleaner\processor\Standard;

/**
 * @covers \voilab\cleaner\processor\Standard
 */
class StandardTest extends TestCase {

    public function setUp()
    {
        $this->cleaner = new HtmlCleaner(new Standard());
    }

    public function testRemoveNotAllowedTags()
    {
        $this->cleaner->addAllowedTags(['span']);
        $p = $this->cleaner->getProcessor();
        $this->assertEquals(
            '<span>test</span>',
            $p->pre($this->cleaner, '<p><span>test</span></p>')
        );
    }

    public function testRemoveNotAllowedTagsButKeepAttributes()
    {
        $this->cleaner->addAllowedTags(['span']);
        $p = $this->cleaner->getProcessor();
        $this->assertEquals(
            '<span class="test">test</span>',
            $p->pre($this->cleaner, '<p><span class="test">test</span></p>')
        );
    }

    public function testRemoveCarriageReturnsInPostProcess()
    {
        $html = "<span>\ntest\n<strong>\ntest\n</strong>\n</span>\n";
        $p = $this->cleaner->getProcessor();
        $this->assertEquals(
            '<span>test<strong>test</strong></span>',
            $p->post($this->cleaner, $html)
        );
    }

    public function testCleanAllTags()
    {
        $this->assertEquals(
            'test',
            $this->cleaner->clean('<p><span>test</span></p>')
        );
    }

    public function testCleanSomeTags()
    {
        $this->cleaner->addAllowedTags(['p']);
        $this->assertEquals(
            '<p>test</p>',
            $this->cleaner->clean('<p><span>test</span></p>')
        );
    }

    public function testRemoveAttributes()
    {
        $this->cleaner->addAllowedTags(['p']);
        $this->assertEquals(
            '<p>test</p>',
            $this->cleaner->clean('<p class="test">test</p>')
        );
    }

    public function testKeepSomeAttributes()
    {
        $this->cleaner->addAllowedTags(['p']);
        $this->cleaner->addAllowedAttributes(['class']);
        $this->assertEquals(
            '<p class="test">test</p>',
            $this->cleaner->clean('<p class="test">test</p>')
        );
    }

    public function testSkippedBadFormattedHtml()
    {
        $this->cleaner->addAllowedTags(['p']);
        $this->assertEquals(
            '<p>test test test</p>',
            $this->cleaner->clean('<p>test <strong>test<em> test</p>')
        );
    }
}
