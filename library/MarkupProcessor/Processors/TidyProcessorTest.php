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

    #[TestDox('returns original markup if tidy processing is disabled via constant')]
    public function testProcessWithTidyDisabled(): void
    {
        if (!extension_loaded('tidy')) {
            $this->markTestSkipped('Tidy extension is not available, cannot test tidy processing.');
        }

        define('DISABLE_HTML_TIDY', true);

        $processor = new TidyProcessor();

        $input = <<<'HTML'
            <!DOCTYPE html><html><body><div><p>Test</p><br></div></body></html>
            HTML;

        $this->assertSame($input, $processor->process($input));
    }
}
