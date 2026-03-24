<?php

namespace Municipio\MarkupProcessor\Processors;

use Municipio\MarkupProcessor\MarkupProcessorInterface;
use WpService\Contracts\ApplyFilters;

class FilterProcessor implements MarkupProcessorInterface
{
    public function __construct(
        private ApplyFilters $wpService,
    ) {}

    public function process(string $markup): string
    {
        return $this->wpService->applyFilters('Municipio\MarkupProcessor', $markup);
    }
}
