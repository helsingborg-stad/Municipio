<?php

namespace Municipio\MarkupProcessor\Processors;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class TidyProcessorTest extends TestCase
{
    #[TestDox('tidies HTML markup using the tidy extension')]
    public function testProcess(): void
    {
        if (!extension_loaded('tidy')) {
            $this->assertTrue(true, 'Tidy extension is not available, skipping test.');
            return;
        }

        $processor = new TidyProcessor();

        $input = <<<'HTML'
            <!DOCTYPE html><html><body><div><p>Test</p><br></div></body></html>
            HTML;

        $this->assertNotSame($input, $processor->process($input));
    }

    #[TestDox('returns original markup if tidy extension is not available')]
    public function testProcessWithoutTidy(): void
    {
        if (extension_loaded('tidy')) {
            $this->assertTrue(true, 'Tidy extension is available, skipping test.');
            return;
        }

        $processor = new TidyProcessor();

        $input = <<<'HTML'
            <!DOCTYPE html><html><body><div><p>Test</p><br></div></body></html>
            HTML;

        $this->assertSame($input, $processor->process($input));
    }

    #[TestDox('does not affect <template> tags during tidy processing')]
    public function testProcessWithTemplateTags(): void
    {
        if (!extension_loaded('tidy')) {
            $this->assertTrue(true, 'Tidy extension is not available, skipping test.');
            return;
        }

        $processor = new TidyProcessor();

        $input = <<<'HTML'
            <!DOCTYPE html><html><body><template><li></template></body></html>
            HTML;

        $this->assertStringContainsString('<template><li></template>', $processor->process($input));
    }

    #[TestDox('does not affect <template> in <template> tags during tidy processing')]
    public function testProcessWithNestedTemplateTags(): void
    {
        if (!extension_loaded('tidy')) {
            $this->assertTrue(true, 'Tidy extension is not available, skipping test.');
            return;
        }

        $processor = new TidyProcessor();

        $input = <<<'HTML'
            <!DOCTYPE html><html><body><template><template><li></template></template></body></html>
            HTML;

        $this->assertStringContainsString('<template><template><li></template></template>', $processor->process($input));
    }
}
