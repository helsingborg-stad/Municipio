<?php

namespace Municipio\Styleguide\Customize\TokenData\Decorators;

use Municipio\Styleguide\Customize\TokenData\TokenData;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\__;
use WpService\Contracts\_x;

class ApplyI18nTest extends TestCase
{
    #[TestDox('all labels and descriptions has a translation')]
    public function testDecorate(): void
    {
        $tokenData = static::getTokenData();
        $decorator = new ApplyI18n(static::createWpService());

        $translatablePaths = static::getTranslatablePaths($tokenData);
        $decoratedData = $decorator->decorate($tokenData);

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
            empty($missingTranslations),
            "Missing {$missingCount} translations:\n- " . implode("\n- ", $missingTranslations),
        );
    }

    private static function getTokenData(): array
    {
        $tokenData = json_decode((string) file_get_contents(TokenData::getFilePath()), true);

        static::assertIsArray($tokenData);

        return $tokenData;
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
            $path = is_int($key) ? sprintf('%s[%d]', $basePath, $key) : ($basePath === '' ? (string) $key : sprintf('%s.%s', $basePath, $key));

            if (is_array($value)) {
                $paths += static::getTranslatablePaths($value, $path);
                continue;
            }

            if (is_string($value) && in_array($key, ['name', 'label', 'description'], true)) {
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

    public static function translate(string $text): string
    {
        return sprintf('__translated__%s', $text);
    }
}
