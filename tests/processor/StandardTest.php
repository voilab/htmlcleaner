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

    public function testKeepAttributeForSpecifictag()
    {
        $this->cleaner->addAllowedTags(['p', 'span']);
        $this->cleaner->addAllowedAttributes([
            new \voilab\cleaner\attribute\Keep('class', 'span')
        ]);
        $this->assertEquals(
            '<p><span class="test">test</span></p>',
            $this->cleaner->clean('<p class="test"><span class="test">test</span></p>')
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

    public function testBadFormattedHtmlTag()
    {
        $this->cleaner->addAllowedTags(['p']);
        $this->expectExceptionMessage('Premature end of data in tag root line 1');
        $this->cleaner->clean('<p>test');
    }

    public function testBadFormattedHtmlContent()
    {
        $this->cleaner->addAllowedTags(['p']);
        $this->expectExceptionMessage('StartTag: invalid element name');
        $this->cleaner->clean('<p>test < than test</p>');
    }

    public function testBadFormattedHtmlContentAmp()
    {
        $this->cleaner->addAllowedTags(['p']);
        $this->expectExceptionMessage('xmlParseEntityRef: no name');
        $this->cleaner->clean('<p>test & than test</p>');
    }

    public function testGreaterThanContentPass()
    {
        $this->cleaner->addAllowedTags(['p']);
        $this->assertEquals(
            '<p>test &gt; than test</p>',
            $this->cleaner->clean('<p>test > than test</p>')
        );
    }

    public function testBadFormattedHtmlAttribute()
    {
        $this->cleaner
            ->addAllowedTags(['p'])
            ->addAllowedAttributes(['class']);

        $this->expectExceptionMessage('Extra content at the end of the document');
        $this->cleaner->clean('<p class=test>test</p>');
    }
}
