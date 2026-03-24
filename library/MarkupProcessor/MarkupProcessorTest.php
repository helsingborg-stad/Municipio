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
}
