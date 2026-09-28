<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ComponentAssets;

class TableMarkupDetector implements MarkupDetectorInterface
{
    public function matches(string $markup): bool
    {
        return preg_match('/<table(?=[\s>])/i', $markup) === 1;
    }

    public function styles(): array
    {
        return ['editor-table' => 'css/components/table.css'];
    }
}
