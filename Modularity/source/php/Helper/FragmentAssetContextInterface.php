<?php

declare(strict_types=1);

namespace Modularity\Helper;

interface FragmentAssetContextInterface
{
    public function beginFragment(): void;

    public function endFragment(): array;

    public function restoreFragment(array $context): void;
}
