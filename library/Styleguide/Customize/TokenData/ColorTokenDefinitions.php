<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\TokenData;

/** Reads colour roles from the installed Styleguide's source of truth. */
class ColorTokenDefinitions
{
    private static ?array $categories = null;
    private static ?array $settings = null;
    private static ?array $pairs = null;
    private static ?array $brandPairs = null;

    /** @return list<array<string, mixed>> */
    public static function getSettings(): array
    {
        return self::$settings ??= array_values(array_filter(
            array_merge(...array_column(self::getCategories(), 'settings')),
            static fn(array $setting): bool => str_starts_with($setting['variable'] ?? '', '--color--'),
        ));
    }

    /** @return array<string, array{background:string, contrast:string, label:string, optional:bool}> */
    public static function getPairs(): array
    {
        return self::$pairs ??= self::getPairsForCategories(self::getCategories());
    }

    public static function getBrandPairs(): array
    {
        return self::$brandPairs ??= self::getPairsForCategories(array_filter(
            self::getCategories(),
            static fn(array $category): bool => in_array($category['id'] ?? '', ['colors-brand', 'colors-brand-palette'], true),
        ));
    }

    public static function getDefault(string $variable): ?string
    {
        return array_column(self::getSettings(), 'default', 'variable')[$variable] ?? null;
    }

    private static function getPairsForCategories(array $categories): array
    {
        $settingsByVariable = array_column(self::getSettings(), null, 'variable');
        $pairs = [];
        foreach ($categories as $category) {
            foreach ($category['settings'] as $setting) {
                $variable = $setting['variable'];
                if (!array_key_exists($variable . '-contrast', $settingsByVariable)) {
                    continue;
                }
                $pairs[substr($variable, strlen('--color--'))] = [
                    'background' => $variable,
                    'contrast' => $variable . '-contrast',
                    'label' => $setting['label'],
                    'optional' => $category['id'] === 'colors-brand-palette',
                ];
            }
        }
        return $pairs;
    }

    private static function getCategories(): array
    {
        if (self::$categories === null) {
            $path = TokenData::getFilePath();
            $contents = is_file($path) ? file_get_contents($path) : false;
            $definitions = $contents === false ? null : json_decode($contents, true);
            self::$categories = is_array($definitions['categories'] ?? null) ? $definitions['categories'] : [];
        }
        return self::$categories;
    }
}
