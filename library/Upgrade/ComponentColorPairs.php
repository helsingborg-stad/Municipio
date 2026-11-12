<?php

declare(strict_types=1);

namespace Municipio\Upgrade;

use Municipio\Helper\ColorSwatches;
use Municipio\Styleguide\Customize\TokenData\ColorTokenDefinitions;

/** Transforms legacy component colours into persistent semantic pairs. */
class ComponentColorPairs
{
    private const LEGACY_VARIABLES = [
        'header' => ['background-color', 'color', 'border-color'],
        'footer' => ['background-color', 'color', 'border-color', 'subfooter-background-color', 'subfooter-color', 'subfooter-separator-color'],
    ];

    public function migrate(string $component, array $values): array
    {
        $prefix = '--c-' . $component . '--';
        $surface = $values[$prefix . 'color--surface'] ?? $values[$prefix . 'background-color'] ?? 'var(--color--surface)';
        foreach (self::LEGACY_VARIABLES[$component] ?? [] as $variable) {
            unset($values[$prefix . $variable]);
        }
        if (!is_string($surface)) {
            return $values;
        }
        $pair = $this->getPair($surface);
        if ($pair !== null) {
            $values[$prefix . 'color--surface'] = 'var(' . $pair['background'] . ')';
            $values[$prefix . 'color--surface-contrast'] = 'var(' . $pair['contrast'] . ')';
        }
        return $values;
    }

    private function getPair(string $surface): ?array
    {
        $pairs = ColorTokenDefinitions::getPairs();
        $backgrounds = array_map(static fn(array $pair): string => 'var(' . $pair['background'] . ')', $pairs);
        $role = array_search(trim($surface), $backgrounds, true);
        if ($role === false) {
            $role = ColorSwatches::getClosestColorPairRole($surface);
        }
        return $pairs[$role] ?? null;
    }
}
