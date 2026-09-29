<?php

namespace Municipio\MarkupProcessor;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MarkupProcessorTest extends TestCase
{
    #[TestDox('process() should takes a string and returns a string')]
    public function testProcessReturnsString(): void
    {
        $processor = new MarkupProcessor(new FakeWpService(['applyFilters' => fn($hookName, $value) => $value]));
        $input = '<div>Hello World</div>';
        $output = $processor->process($input);
        $this->assertIsString($output);
    }

    #[TestDox('process() displays double-escaped labels as ampersands')]
    public function testProcessNormalizesAmpersandsInLabels(): void
    {
        $processor = new MarkupProcessor(new FakeWpService(['applyFilters' => fn($hookName, $value) => $value]));
        $input = '<!DOCTYPE html><html><head><title>R&amp;amp;D</title></head>'
            . '<body><a href="/?q=R&amp;amp;D" aria-label="R&amp;amp;D">R&amp;amp;D & team</a></body></html>';

        $output = $processor->process($input);

        $this->assertMatchesRegularExpression('/<title>\s*R&amp;D\s*<\/title>/', $output);
        $this->assertStringContainsString('href="/?q=R&amp;amp;D"', $output);
        $this->assertStringContainsString('aria-label="R&amp;D"', $output);
        $this->assertStringContainsString('R&amp;D &amp; team', $output);
    }
}
