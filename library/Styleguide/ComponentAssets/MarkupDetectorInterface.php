<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ComponentAssets;

interface MarkupDetectorInterface
{
    public function matches(string $markup): bool;

    /** @return array<string, string> Style handles mapped to CSS paths. */
    public function styles(): array;

    /** @return list<string> Component slugs to enqueue. */
    public function components(): array;
}
