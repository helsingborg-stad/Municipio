<?php

declare(strict_types=1);

namespace Municipio\ImagePreload;

interface LqipPreloadConfigInterface
{
    public function isEnabled(): bool;

    public function maxPreloads(): int;

    public function fetchPriority(): string;
}
