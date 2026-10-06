<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ComponentAssets;

/** Enqueues FAB styles for standalone buttons decorated with the c-fab class. */
class FabMarkupDetector implements MarkupDetectorInterface
{
    public function matches(string $markup): bool
    {
        return preg_match('/\bclass=["\'][^"\']*\bc-fab\b/i', $markup) === 1;
    }

    public function styles(): array
    {
        return [];
    }

    public function components(): array
    {
        return ['fab'];
    }
}
