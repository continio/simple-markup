<?php
declare(strict_types=1);

namespace Continio\Tests\SimpleMarkup;

use Continio\SimpleMarkup\SimpleMarkup;
use PHPUnit\Framework\TestCase;

class SimpleMarkupTest extends TestCase
{
    /** @test */
    public function testBoldHtmlIsReturned()
    {
        $markup = (new SimpleMarkup("This *text* should be bold."))
            ->bold()
            ->parse();
        
        $this->assertEquals('This <strong>text</strong> should be bold.', $markup);
    }

    /** @test */
    public function testItalicHtmlIsReturned()
    {
        $markup = (new SimpleMarkup("This ~text~ should be italic."))
            ->italic()
            ->parse();
        
        $this->assertEquals('This <em>text</em> should be italic.', $markup);
    }

    /** @test */
    public function testUnderlineHtmlIsReturned()
    {
        $markup = (new SimpleMarkup("This _text_ should be underlined."))
            ->underline()
            ->parse();
        
        $this->assertEquals('This <u>text</u> should be underlined.', $markup);
    }

    /** @test */
    public function testDelHtmlIsReturned()
    {
        $markup = (new SimpleMarkup("This -text- should be deleted."))
            ->del()
            ->parse();
        
        $this->assertEquals('This <del>text</del> should be deleted.', $markup);
    }

    /** @test */
    public function testLinkAreReplaced()
    {
        $markup = (new SimpleMarkup("https://www.google.com becomes a link."))
            ->links()
            ->parse();
        
        return $this->assertEquals(
            '<a href="https://www.google.com" target="_blank" rel="nofollow">https://www.google.com</a> becomes a link.',
            $markup
        );
    }

    /** @test */
    public function testExampleStringIsFormattedCorrectly()
    {
        $input = 'This is an https://example.com of how the *SimpleMarkup* library works. It should _underline_, ~italicise~ and -delete- text. But it should not react to __doubles__.';
        $expectedOutput = 'This is an <a href="https://example.com" target="_blank" rel="nofollow">https://example.com</a> of how the <strong>SimpleMarkup</strong> library works. It should <u>underline</u>, <em>italicise</em> and <del>delete</del> text. But it should not react to __doubles__.';

        $markup = (new SimpleMarkup($input))
            ->all()
            ->parse();
        
        $this->assertEquals($expectedOutput, $markup);
    }
}