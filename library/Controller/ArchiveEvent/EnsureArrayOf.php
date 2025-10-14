<?php

namespace Municipio\Controller\ArchiveEvent;

class EnsureArrayOf
{
    public static function ensureArrayOf($value, $ensuredType): array
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        return array_filter($value, fn($item) => is_a($item, $ensuredType));
    }
}
