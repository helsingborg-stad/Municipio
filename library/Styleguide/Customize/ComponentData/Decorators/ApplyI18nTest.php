<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\ComponentData\Decorators;

use Municipio\Styleguide\Customize\ComponentData\ComponentData;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\_x;

class ApplyI18nTest extends TestCase
{
    #[TestDox('all labels and descriptions has a translation')]
    public function testDecorate(): void
    {
        $componentData = static::getComponentData();
        $decorator = new ApplyI18n(static::createWpService());

        $translatablePaths = static::getTranslatablePaths($componentData);
        $decoratedData = $decorator->decorate($componentData);

        static::assertNotEmpty($translatablePaths);

        $missingTranslations = [];

        foreach ($translatablePaths as $path => $originalValue) {
            $translatedValue = static::getValueByPath($decoratedData, $path);
            $expectedValue = static::translate($originalValue);

            if ($translatedValue !== $expectedValue) {
                $missingTranslations[] = sprintf(
                    '%s => %s',
                    $path,
                    var_export($originalValue, true),
                );
            }
        }

        $missingCount = count($missingTranslations);

        static::assertTrue(
            $missingTranslations === [],
            "Missing {$missingCount} translations:\n- " . implode("\n- ", $missingTranslations),
        );
    }

    #[TestDox('all declared translations are used by component data')]
    public function testDecoratorHasNoUnusedTranslations(): void
    {
        $translatableStrings = array_values(array_unique(static::getTranslatablePaths(static::getComponentData())));
        $declaredTranslations = static::getDeclaredTranslations();
        $unusedTranslations = array_values(array_diff($declaredTranslations, $translatableStrings));

        sort($unusedTranslations);

        $unusedCount = count($unusedTranslations);

        static::assertTrue(
            $unusedTranslations === [],
            "Found {$unusedCount} unused translations:\n- " . implode("\n- ", $unusedTranslations),
        );
    }

    private static function getComponentData(): array
    {
        $componentData = json_decode((string) file_get_contents(ComponentData::getFilePath()), true);

        static::assertIsArray($componentData);

        return $componentData;
    }

    private static function createWpService(): _x
    {
        return new class implements _x {
            public function _x(string $text, string $context, string $domain = 'default'): string
            {
                return ApplyI18nTest::translate($text);
            }
        };
    }

    private static function getTranslatablePaths(array $data, string $basePath = ''): array
    {
        $paths = [];

        foreach ($data as $key => $value) {
            $path = (string) $key;

            if (is_int($key)) {
                $path = sprintf('%s[%d]', $basePath, $key);
            }

            if (!is_int($key) && $basePath !== '') {
                $path = sprintf('%s.%s', $basePath, $key);
            }

            if (is_array($value)) {
                $paths += static::getTranslatablePaths($value, $path);
                continue;
            }

            if (is_string($value) && in_array($key, ['name', 'description', 'label'], true)) {
                $paths[$path] = $value;
            }
        }

        return $paths;
    }

    private static function getValueByPath(array $data, string $path): mixed
    {
        preg_match_all('/([^.\\[\\]]+)|\[(\d+)\]/', $path, $matches, PREG_SET_ORDER);

        $currentValue = $data;

        foreach ($matches as $match) {
            $segment = $match[1] !== '' ? $match[1] : (int) $match[2];

            if (!is_array($currentValue) || !array_key_exists($segment, $currentValue)) {
                return null;
            }

            $currentValue = $currentValue[$segment];
        }

        return $currentValue;
    }

    private static function getDeclaredTranslations(): array
    {
        $source = file_get_contents((string) (new \ReflectionClass(ApplyI18n::class))->getFileName());

        static::assertNotFalse($source);
        preg_match_all("/^[\t ]+'((?:\\\\'|[^'])*)' =>/m", $source, $matches);

        $translations = array_map('stripcslashes', $matches[1]);
        $translations = array_values(array_unique($translations));

        sort($translations);

        return $translations;
    }

    public static function translate(string $text): string
    {
        return sprintf('__translated__%s', $text);
    }
}
