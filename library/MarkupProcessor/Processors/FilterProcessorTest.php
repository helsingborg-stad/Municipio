<?php

namespace Municipio\MarkupProcessor\Processors;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\ApplyFilters;

class FilterProcessorTest extends TestCase
{
    #[TestDox('applies filters to the markup')]
    public function testProcess(): void
    {
        $wpService = new class implements ApplyFilters {
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return 'filtered markup';
            }
        };

        $processor = new FilterProcessor($wpService);

        $this->assertSame('filtered markup', $processor->process('original markup'));
    }
}
